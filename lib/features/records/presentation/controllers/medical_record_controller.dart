import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_medical_record_repository.dart';
import '../../domain/models/medical_record_model.dart';
import '../../domain/repositories/medical_record_repository.dart';

final medicalRecordRepositoryProvider = Provider<MedicalRecordRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiMedicalRecordRepository(apiClient: apiClient);
});

final medicalRecordsProvider = FutureProvider<List<MedicalRecordModel>>((ref) async {
  final repository = ref.watch(medicalRecordRepositoryProvider);
  return repository.getMedicalRecords();
});

final medicalRecordDetailProvider = FutureProvider.family<MedicalRecordModel?, String>((ref, id) async {
  final repository = ref.watch(medicalRecordRepositoryProvider);
  return repository.getRecordById(id);
});
