import '../../../../core/network/api_client.dart';
import '../../domain/models/doctor_model.dart';
import '../../domain/repositories/doctor_repository.dart';

class ApiDoctorRepository implements DoctorRepository {
  final ApiClient _apiClient;

  ApiDoctorRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<SpecialtyModel>> getSpecialties() async {
    final response = await _apiClient.get('/specialties');
    final data = response.data['data'] as List? ?? [];

    final list = data
        .map((item) => SpecialtyModel.fromJson(item as Map<String, dynamic>))
        .toList();

    // Include 'All' filter item at the beginning
    final totalCount = list.fold<int>(0, (sum, item) => sum + item.doctorCount);
    return [
      SpecialtyModel(
        id: 'all',
        name: 'All Doctors',
        iconName: 'medical_services',
        doctorCount: totalCount,
      ),
      ...list,
    ];
  }

  @override
  Future<List<DoctorModel>> getDoctors({
    String? searchQuery,
    String? specialtyId,
    double? minRating,
    double? maxFee,
    bool? onlyAvailableToday,
  }) async {
    final queryParams = <String, dynamic>{};
    if (searchQuery != null && searchQuery.trim().isNotEmpty) {
      queryParams['search'] = searchQuery.trim();
    }
    if (specialtyId != null && specialtyId != 'all' && specialtyId.isNotEmpty) {
      queryParams['specialty_id'] = specialtyId;
    }

    final response = await _apiClient.get('/doctors', queryParameters: queryParams);
    final data = response.data['data'] as List? ?? [];

    var doctors = data
        .map((item) => DoctorModel.fromJson(item as Map<String, dynamic>))
        .toList();

    if (minRating != null && minRating > 0) {
      doctors = doctors.where((d) => d.rating >= minRating).toList();
    }
    if (maxFee != null && maxFee > 0) {
      doctors = doctors.where((d) => d.consultationFee <= maxFee).toList();
    }

    return doctors;
  }

  @override
  Future<List<DoctorModel>> getRecommendedDoctors() async {
    final response = await _apiClient.get('/doctors', queryParameters: {'per_page': 4});
    final data = response.data['data'] as List? ?? [];

    return data
        .map((item) => DoctorModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<DoctorModel?> getDoctorById(String id) async {
    try {
      final response = await _apiClient.get('/doctors/$id');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        return DoctorModel.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }
}
