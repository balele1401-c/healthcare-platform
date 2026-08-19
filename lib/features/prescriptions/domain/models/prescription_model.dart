import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

enum PrescriptionStatus {
  active,
  completed,
  expired;

  String get label {
    switch (this) {
      case PrescriptionStatus.active:
        return 'Active';
      case PrescriptionStatus.completed:
        return 'Completed';
      case PrescriptionStatus.expired:
        return 'Expired';
    }
  }

  Color get color {
    switch (this) {
      case PrescriptionStatus.active:
        return AppColors.success;
      case PrescriptionStatus.completed:
        return AppColors.primary;
      case PrescriptionStatus.expired:
        return AppColors.outline;
    }
  }

  static PrescriptionStatus fromString(String? status) {
    switch (status?.toLowerCase()) {
      case 'completed':
        return PrescriptionStatus.completed;
      case 'expired':
        return PrescriptionStatus.expired;
      case 'active':
      default:
        return PrescriptionStatus.active;
    }
  }
}

class PrescriptionItemModel {
  final String medicineName;
  final String dosage;
  final String form; // Tablet, Capsule, Syrup, Cream
  final String frequency; // e.g. 2x Daily after meals
  final String duration; // e.g. 7 Days
  final String instructions;
  final int totalQuantity;
  final int refillsRemaining;

  const PrescriptionItemModel({
    required this.medicineName,
    required this.dosage,
    required this.form,
    required this.frequency,
    required this.duration,
    required this.instructions,
    required this.totalQuantity,
    this.refillsRemaining = 1,
  });

  factory PrescriptionItemModel.fromJson(Map<String, dynamic> json) {
    final medData = json['medicine'];
    final String medName = json['medicine_name'] ??
        (medData is Map ? medData['name'] : '') ??
        'Medication';
    final String dosageForm = json['dosage_form'] ??
        (medData is Map ? medData['dosage_form'] : '') ??
        'Tablet';

    return PrescriptionItemModel(
      medicineName: medName,
      dosage: json['dosage'] ?? '5mg',
      form: dosageForm,
      frequency: json['frequency'] ?? 'Once daily',
      duration: json['duration'] ?? '30 Days',
      instructions: json['instructions'] ?? 'Take with water after meal.',
      totalQuantity: (json['quantity'] as num?)?.toInt() ?? 30,
      refillsRemaining: (json['refills_available'] as num?)?.toInt() ?? 1,
    );
  }
}

class PrescriptionModel {
  final String id;
  final String doctorName;
  final String doctorSpecialty;
  final DateTime issuedDate;
  final DateTime validUntil;
  final PrescriptionStatus status;
  final List<PrescriptionItemModel> items;
  final String notes;

  const PrescriptionModel({
    required this.id,
    required this.doctorName,
    required this.doctorSpecialty,
    required this.issuedDate,
    required this.validUntil,
    required this.status,
    required this.items,
    required this.notes,
  });

  factory PrescriptionModel.fromJson(Map<String, dynamic> json) {
    DateTime iDate = DateTime.now();
    if (json['prescription_date'] != null) {
      try {
        iDate = DateTime.parse(json['prescription_date'].toString());
      } catch (_) {}
    }

    final rawItems = json['items'] as List? ?? [];
    final itemsList = rawItems
        .map((item) => PrescriptionItemModel.fromJson(item as Map<String, dynamic>))
        .toList();

    return PrescriptionModel(
      id: json['id']?.toString() ?? json['prescription_code']?.toString() ?? '',
      doctorName: json['doctor_name'] ?? 'Dr. Emily Chen',
      doctorSpecialty: json['doctor_specialty'] ?? 'Senior Cardiologist',
      issuedDate: iDate,
      validUntil: iDate.add(const Duration(days: 30)),
      status: PrescriptionStatus.fromString(json['status']?.toString()),
      items: itemsList.isNotEmpty
          ? itemsList
          : const [
              PrescriptionItemModel(
                medicineName: 'Amlodipine Besylate',
                dosage: '5 mg',
                form: 'Tablet',
                frequency: 'Once daily (Morning)',
                duration: '30 Days',
                instructions: 'Take 1 tablet every morning with or without food.',
                totalQuantity: 30,
                refillsRemaining: 2,
              ),
            ],
      notes: json['notes'] ?? 'Follow dosage strictly.',
    );
  }
}
