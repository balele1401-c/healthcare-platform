/// Domain model for initial health profile setup.
class HealthProfileModel {
  final DateTime dateOfBirth;
  final String gender;
  final String? bloodType;
  final double? heightCm;
  final double? weightKg;
  final String? emergencyContactName;
  final String? emergencyContactPhone;
  final String? allergies;

  const HealthProfileModel({
    required this.dateOfBirth,
    required this.gender,
    this.bloodType,
    this.heightCm,
    this.weightKg,
    this.emergencyContactName,
    this.emergencyContactPhone,
    this.allergies,
  });

  Map<String, dynamic> toJson() {
    return {
      'date_of_birth': dateOfBirth.toIso8601String(),
      'gender': gender,
      'blood_type': bloodType,
      'height_cm': heightCm,
      'weight_kg': weightKg,
      'emergency_contact_name': emergencyContactName,
      'emergency_contact_phone': emergencyContactPhone,
      'allergies': allergies,
    };
  }

  factory HealthProfileModel.fromJson(Map<String, dynamic> json) {
    return HealthProfileModel(
      dateOfBirth: DateTime.parse(json['date_of_birth']),
      gender: json['gender'] ?? 'other',
      bloodType: json['blood_type'],
      heightCm: json['height_cm'] != null ? (json['height_cm'] as num).toDouble() : null,
      weightKg: json['weight_kg'] != null ? (json['weight_kg'] as num).toDouble() : null,
      emergencyContactName: json['emergency_contact_name'],
      emergencyContactPhone: json['emergency_contact_phone'],
      allergies: json['allergies'],
    );
  }
}
