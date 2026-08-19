import '../models/prescription_model.dart';

abstract class PrescriptionRepository {
  Future<List<PrescriptionModel>> getPrescriptions({PrescriptionStatus? status});

  Future<PrescriptionModel?> getPrescriptionById(String id);
}
