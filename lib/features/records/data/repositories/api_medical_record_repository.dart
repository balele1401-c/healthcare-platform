import '../../../../core/network/api_client.dart';
import '../../domain/models/medical_record_model.dart';
import '../../domain/repositories/medical_record_repository.dart';

class ApiMedicalRecordRepository implements MedicalRecordRepository {
  final ApiClient _apiClient;

  ApiMedicalRecordRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<MedicalRecordModel>> getMedicalRecords() async {
    final response = await _apiClient.get('/patient/medical-records');
    final data = response.data['data'] as List? ?? [];

    return data
        .map((item) => MedicalRecordModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<MedicalRecordModel?> getRecordById(String id) async {
    try {
      final response = await _apiClient.get('/medical-records/$id');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        return MedicalRecordModel.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }
}
