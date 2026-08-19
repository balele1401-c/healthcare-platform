import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/doctor/data/repositories/mock_doctor_repository.dart';

void main() {
  late MockDoctorRepository repository;

  setUp(() {
    repository = MockDoctorRepository();
  });

  group('MockDoctorRepository Tests', () {
    test('getSpecialties returns full list of medical specialties', () async {
      final specialties = await repository.getSpecialties();
      expect(specialties.isNotEmpty, true);
      expect(specialties.first.id, 'all');
      expect(specialties.any((s) => s.id == 'cardiology'), true);
    });

    test('getDoctors returns all doctors when no filter is applied', () async {
      final doctors = await repository.getDoctors();
      expect(doctors.length, 6);
    });

    test('getDoctors filters by search query', () async {
      final results = await repository.getDoctors(searchQuery: 'Emily');
      expect(results.length, 1);
      expect(results.first.name, 'Dr. Emily Chen');
    });

    test('getDoctors filters by specialty', () async {
      final results = await repository.getDoctors(specialtyId: 'neurology');
      expect(results.length, 1);
      expect(results.first.name, 'Dr. Sophia Rodriguez');
    });

    test('getDoctors filters by max consultation fee', () async {
      final results = await repository.getDoctors(maxFee: 70.0);
      expect(results.every((d) => d.consultationFee <= 70.0), true);
    });

    test('getDoctors filters by minimum rating', () async {
      final results = await repository.getDoctors(minRating: 4.9);
      expect(results.every((d) => d.rating >= 4.9), true);
    });

    test('getDoctorById returns correct doctor or null', () async {
      final doctor = await repository.getDoctorById('doc_1');
      expect(doctor, isNotNull);
      expect(doctor!.name, 'Dr. Emily Chen');

      final notFound = await repository.getDoctorById('non_existent');
      expect(notFound, isNull);
    });
  });
}
