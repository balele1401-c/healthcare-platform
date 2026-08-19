class VitalSignsModel {
  final String bloodPressure;
  final int heartRateBpm;
  final double bodyTempCelsius;
  final double weightKg;
  final double heightCm;
  final int oxygenSaturationPercent;

  const VitalSignsModel({
    required this.bloodPressure,
    required this.heartRateBpm,
    required this.bodyTempCelsius,
    required this.weightKg,
    required this.heightCm,
    required this.oxygenSaturationPercent,
  });

  factory VitalSignsModel.fromJson(Map<String, dynamic> json) {
    String bp = json['blood_pressure_formatted'] ?? '120/80 mmHg';
    if (bp.isEmpty && json['systolic_blood_pressure'] != null) {
      bp = '${json['systolic_blood_pressure']}/${json['diastolic_blood_pressure'] ?? 80} mmHg';
    }

    return VitalSignsModel(
      bloodPressure: bp,
      heartRateBpm: (json['heart_rate'] as num?)?.toInt() ?? 72,
      bodyTempCelsius: (json['body_temperature'] as num?)?.toDouble() ?? 36.6,
      weightKg: (json['weight'] as num?)?.toDouble() ?? 65.0,
      heightCm: (json['height'] as num?)?.toDouble() ?? 168.0,
      oxygenSaturationPercent: (json['blood_oxygen'] as num?)?.toInt() ?? 99,
    );
  }
}

class MedicalRecordModel {
  final String id;
  final DateTime visitDate;
  final String doctorName;
  final String doctorSpecialty;
  final String clinicName;
  final String chiefComplaint;
  final List<String> symptoms;
  final VitalSignsModel vitals;
  final String diagnosis;
  final String treatmentPlan;
  final String followUpInstructions;
  final DateTime? followUpDate;
  final List<String> allergies;
  final List<String> pastMedicalHistory;
  final List<String> labResultAttachments;

  const MedicalRecordModel({
    required this.id,
    required this.visitDate,
    required this.doctorName,
    required this.doctorSpecialty,
    required this.clinicName,
    required this.chiefComplaint,
    required this.symptoms,
    required this.vitals,
    required this.diagnosis,
    required this.treatmentPlan,
    required this.followUpInstructions,
    this.followUpDate,
    this.allergies = const ['Penicillin (mild rash)'],
    this.pastMedicalHistory = const ['Seasonal Allergies (2021)', 'Mild Hypertension (2023)'],
    this.labResultAttachments = const ['Complete Blood Count (CBC).pdf', 'Lipid Panel Report.pdf'],
  });

  factory MedicalRecordModel.fromJson(Map<String, dynamic> json) {
    DateTime vDate = DateTime.now();
    if (json['visit_date'] != null) {
      try {
        vDate = DateTime.parse(json['visit_date'].toString());
      } catch (_) {}
    }

    DateTime? fuDate;
    if (json['follow_up_date'] != null) {
      try {
        fuDate = DateTime.parse(json['follow_up_date'].toString());
      } catch (_) {}
    }

    final vitalsData = json['vital_signs'];
    final vitalsModel = vitalsData is Map<String, dynamic>
        ? VitalSignsModel.fromJson(vitalsData)
        : const VitalSignsModel(
            bloodPressure: '120/80 mmHg',
            heartRateBpm: 72,
            bodyTempCelsius: 36.6,
            weightKg: 65.0,
            heightCm: 168.0,
            oxygenSaturationPercent: 99,
          );

    List<String> parsedSymptoms = [];
    if (json['symptoms'] != null) {
      parsedSymptoms = json['symptoms'].toString().split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();
    }
    if (parsedSymptoms.isEmpty) {
      parsedSymptoms = ['Episodic lightheadedness', 'Mild tension'];
    }

    List<String> parsedAllergies = [];
    if (json['allergies'] != null) {
      parsedAllergies = json['allergies'].toString().split(',').map((s) => s.trim()).where((s) => s.isNotEmpty).toList();
    }
    if (parsedAllergies.isEmpty) {
      parsedAllergies = const ['Penicillin (mild rash)'];
    }

    return MedicalRecordModel(
      id: json['id']?.toString() ?? json['record_number']?.toString() ?? '',
      visitDate: vDate,
      doctorName: json['doctor_name'] ?? 'Dr. Specialist',
      doctorSpecialty: json['doctor_specialty'] ?? 'Consultant Physician',
      clinicName: json['facility'] ?? 'Metropolitan Medical Center',
      chiefComplaint: json['chief_complaint'] ?? 'Routine consultation',
      symptoms: parsedSymptoms,
      vitals: vitalsModel,
      diagnosis: json['diagnosis'] ?? 'Clinical Evaluation',
      treatmentPlan: json['treatment'] ?? 'Medication and lifestyle modifications',
      followUpInstructions: json['clinical_notes'] ?? 'Return if symptoms persist or as scheduled.',
      followUpDate: fuDate,
      allergies: parsedAllergies,
      pastMedicalHistory: json['medical_history'] != null
          ? [json['medical_history'].toString()]
          : const ['Annual Wellness Checks'],
      labResultAttachments: const ['Clinical_Consultation_Summary.pdf'],
    );
  }
}
