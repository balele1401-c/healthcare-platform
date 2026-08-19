import '../../domain/models/prescription_model.dart';
import '../../domain/repositories/prescription_repository.dart';

class MockPrescriptionRepository implements PrescriptionRepository {
  static final List<PrescriptionModel> _prescriptions = [
    PrescriptionModel(
      id: 'RX-88291',
      doctorName: 'Dr. Emily Chen',
      doctorSpecialty: 'Senior Cardiologist',
      issuedDate: DateTime.now().subtract(const Duration(days: 5)),
      validUntil: DateTime.now().add(const Duration(days: 85)),
      status: PrescriptionStatus.active,
      notes: 'Take daily in morning. Check blood pressure twice weekly.',
      items: const [
        PrescriptionItemModel(
          medicineName: 'Amlodipine Besylate',
          dosage: '5 mg',
          form: 'Oral Tablet',
          frequency: 'Once Daily (Morning)',
          duration: '90 Days',
          instructions: 'Take with or without food. Avoid grapefruit juice.',
          totalQuantity: 90,
          refillsRemaining: 2,
        ),
        PrescriptionItemModel(
          medicineName: 'Coenzyme Q10 (Ubiquinone)',
          dosage: '100 mg',
          form: 'Softgel Capsule',
          frequency: 'Once Daily (with breakfast)',
          duration: '60 Days',
          instructions: 'Take with dietary fats for enhanced absorption.',
          totalQuantity: 60,
          refillsRemaining: 1,
        ),
      ],
    ),
    PrescriptionModel(
      id: 'RX-77410',
      doctorName: 'Dr. Marcus Vance',
      doctorSpecialty: 'Dermatologist',
      issuedDate: DateTime.now().subtract(const Duration(days: 20)),
      validUntil: DateTime.now().add(const Duration(days: 10)),
      status: PrescriptionStatus.active,
      notes: 'Apply thin layer sparingly to affected skin areas at night.',
      items: const [
        PrescriptionItemModel(
          medicineName: 'Hydrocortisone Butyrate 0.1%',
          dosage: '30 g Tube',
          form: 'Topical Cream',
          frequency: 'Once Nightly',
          duration: '30 Days',
          instructions: 'Wash hands before and after application. Do not apply near eyes.',
          totalQuantity: 1,
          refillsRemaining: 0,
        ),
      ],
    ),
    PrescriptionModel(
      id: 'RX-65120',
      doctorName: 'Dr. Sophia Rodriguez',
      doctorSpecialty: 'Neurologist',
      issuedDate: DateTime.now().subtract(const Duration(days: 90)),
      validUntil: DateTime.now().subtract(const Duration(days: 30)),
      status: PrescriptionStatus.completed,
      notes: 'Course completed successfully.',
      items: const [
        PrescriptionItemModel(
          medicineName: 'Magnesium Glycinate',
          dosage: '400 mg',
          form: 'Tablet',
          frequency: 'Once Nightly before bed',
          duration: '60 Days',
          instructions: 'Promotes muscle relaxation and sleep quality.',
          totalQuantity: 60,
          refillsRemaining: 0,
        ),
      ],
    ),
    PrescriptionModel(
      id: 'RX-42099',
      doctorName: 'Dr. James Wilson',
      doctorSpecialty: 'Family Medicine',
      issuedDate: DateTime.now().subtract(const Duration(days: 200)),
      validUntil: DateTime.now().subtract(const Duration(days: 110)),
      status: PrescriptionStatus.expired,
      notes: 'Prescription expired. Consultation required for renewal.',
      items: const [
        PrescriptionItemModel(
          medicineName: 'Amoxicillin Trihydrate',
          dosage: '500 mg',
          form: 'Capsule',
          frequency: '3x Daily (every 8 hours)',
          duration: '10 Days',
          instructions: 'Complete full course even if symptoms resolve earlier.',
          totalQuantity: 30,
          refillsRemaining: 0,
        ),
      ],
    ),
  ];

  @override
  Future<List<PrescriptionModel>> getPrescriptions({PrescriptionStatus? status}) async {
    await Future.delayed(const Duration(milliseconds: 250));
    if (status == null) {
      return List.unmodifiable(_prescriptions);
    }
    return _prescriptions.where((p) => p.status == status).toList();
  }

  @override
  Future<PrescriptionModel?> getPrescriptionById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _prescriptions.firstWhere((p) => p.id == id);
    } catch (_) {
      return _prescriptions.first;
    }
  }
}
