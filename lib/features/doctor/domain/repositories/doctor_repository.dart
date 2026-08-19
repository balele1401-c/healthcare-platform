import '../models/doctor_model.dart';

abstract class DoctorRepository {
  Future<List<SpecialtyModel>> getSpecialties();

  Future<List<DoctorModel>> getDoctors({
    String? searchQuery,
    String? specialtyId,
    double? minRating,
    double? maxFee,
    bool? onlyAvailableToday,
  });

  Future<List<DoctorModel>> getRecommendedDoctors();

  Future<DoctorModel?> getDoctorById(String id);
}
