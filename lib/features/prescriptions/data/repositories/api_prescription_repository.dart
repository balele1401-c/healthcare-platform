import '../../../../core/network/api_client.dart';
import '../../domain/models/prescription_model.dart';
import '../../domain/repositories/prescription_repository.dart';

class ApiPrescriptionRepository implements PrescriptionRepository {
  final ApiClient _apiClient;

  ApiPrescriptionRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<PrescriptionModel>> getPrescriptions({PrescriptionStatus? status}) async {
    final queryParams = <String, dynamic>{};
    if (status != null) {
      queryParams['status'] = status.name;
    }

    final response = await _apiClient.get('/patient/prescriptions', queryParameters: queryParams);
    final data = response.data['data'] as List? ?? [];

    return data
        .map((item) => PrescriptionModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<PrescriptionModel?> getPrescriptionById(String id) async {
    try {
      final response = await _apiClient.get('/prescriptions/$id');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        return PrescriptionModel.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }
}
