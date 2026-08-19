import '../../domain/models/medical_record_model.dart';
import '../../domain/repositories/medical_record_repository.dart';

class MockMedicalRecordRepository implements MedicalRecordRepository {
  static final List<MedicalRecordModel> _records = [
    MedicalRecordModel(
      id: 'REC-2024-001',
      visitDate: DateTime.now().subtract(const Duration(days: 14)),
      doctorName: 'Dr. Sophia Rodriguez',
      doctorSpecialty: 'Neurologist',
      clinicName: 'NeuroHealth Comprehensive Care Center',
      chiefComplaint: 'Frequent bilateral frontal tension headaches for 3 weeks.',
      symptoms: ['Mild photophobia', 'Neck muscle tightness', 'Sleep onset latency'],
      vitals: const VitalSignsModel(
        bloodPressure: '118/76',
        heartRateBpm: 72,
        bodyTempCelsius: 36.6,
        weightKg: 64.5,
        heightCm: 168.0,
        oxygenSaturationPercent: 99,
      ),
      diagnosis: 'Episodic tension-type headache (G44.2). Mild muscular strain of cervical spine.',
      treatmentPlan: '1. Magnesium glycinate 400mg daily.\n2. Sleep hygiene optimization.\n3. Physical therapy referral for cervical posture.',
      followUpInstructions: 'Return in 6 weeks if headache frequency exceeds 2 episodes per week.',
      followUpDate: DateTime.now().add(const Duration(days: 28)),
      allergies: const ['Penicillin (mild rash)'],
      pastMedicalHistory: const ['Seasonal allergic rhinitis', 'Mild myopia'],
    ),
    MedicalRecordModel(
      id: 'REC-2024-002',
      visitDate: DateTime.now().subtract(const Duration(days: 45)),
      doctorName: 'Dr. James Wilson',
      doctorSpecialty: 'Family Medicine',
      clinicName: 'Community Health Partners',
      chiefComplaint: 'Annual comprehensive wellness physical examination.',
      symptoms: ['Asymptomatic'],
      vitals: const VitalSignsModel(
        bloodPressure: '120/80',
        heartRateBpm: 70,
        bodyTempCelsius: 36.5,
        weightKg: 65.0,
        heightCm: 168.0,
        oxygenSaturationPercent: 99,
      ),
      diagnosis: 'Healthy adult examination (Z00.00). Normal cardiovascular & metabolic screen.',
      treatmentPlan: '1. Continue balanced Mediterranean diet.\n2. Maintain 150 mins weekly moderate aerobic activity.\n3. Updated tetanus-diphtheria-pertussis (Tdap) booster.',
      followUpInstructions: 'Routine annual follow-up in 12 months.',
      followUpDate: DateTime.now().add(const Duration(days: 320)),
      allergies: const ['Penicillin (mild rash)'],
      pastMedicalHistory: const ['Seasonal allergic rhinitis'],
    ),
    MedicalRecordModel(
      id: 'REC-2023-089',
      visitDate: DateTime.now().subtract(const Duration(days: 180)),
      doctorName: 'Dr. Emily Chen',
      doctorSpecialty: 'Senior Cardiologist',
      clinicName: 'Metropolitan Heart & Vascular Institute',
      chiefComplaint: 'Evaluation of occasional palpitations after high caffeine intake.',
      symptoms: ['Brief flutter sensation', 'No syncope', 'No chest tightness'],
      vitals: const VitalSignsModel(
        bloodPressure: '124/82',
        heartRateBpm: 78,
        bodyTempCelsius: 36.7,
        weightKg: 65.2,
        heightCm: 168.0,
        oxygenSaturationPercent: 98,
      ),
      diagnosis: 'Isolated benign premature ventricular complexes (PVCs). Normal resting 12-lead ECG.',
      treatmentPlan: '1. Reduce caffeine to <200mg/day.\n2. Adequate hydration with electrolyte balance.\n3. Reassurance given.',
      followUpInstructions: 'Seek immediate care if palpitations are accompanied by dizziness or chest discomfort.',
      allergies: const ['Penicillin (mild rash)'],
      pastMedicalHistory: const ['None'],
    ),
  ];

  @override
  Future<List<MedicalRecordModel>> getMedicalRecords() async {
    await Future.delayed(const Duration(milliseconds: 250));
    return List.unmodifiable(_records);
  }

  @override
  Future<MedicalRecordModel?> getRecordById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _records.firstWhere((r) => r.id == id);
    } catch (_) {
      return _records.first;
    }
  }
}
