import '../models/medical_record_model.dart';

abstract class MedicalRecordRepository {
  Future<List<MedicalRecordModel>> getMedicalRecords();

  Future<MedicalRecordModel?> getRecordById(String id);
}
