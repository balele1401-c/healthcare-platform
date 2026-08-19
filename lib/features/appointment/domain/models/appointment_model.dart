import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

enum AppointmentStatus {
  upcoming,
  completed,
  cancelled;

  String get label {
    switch (this) {
      case AppointmentStatus.upcoming:
        return 'Upcoming';
      case AppointmentStatus.completed:
        return 'Completed';
      case AppointmentStatus.cancelled:
        return 'Cancelled';
    }
  }

  Color get color {
    switch (this) {
      case AppointmentStatus.upcoming:
        return AppColors.primary;
      case AppointmentStatus.completed:
        return AppColors.success;
      case AppointmentStatus.cancelled:
        return AppColors.error;
    }
  }

  static AppointmentStatus fromString(String? status) {
    switch (status?.toLowerCase()) {
      case 'completed':
        return AppointmentStatus.completed;
      case 'cancelled':
        return AppointmentStatus.cancelled;
      case 'pending':
      case 'confirmed':
      case 'in_consultation':
      case 'upcoming':
      default:
        return AppointmentStatus.upcoming;
    }
  }
}

enum ConsultationType {
  inPerson,
  videoCall;

  String get label {
    switch (this) {
      case ConsultationType.inPerson:
        return 'In-Person Visit';
      case ConsultationType.videoCall:
        return 'Video Consultation';
    }
  }

  IconData get icon {
    switch (this) {
      case ConsultationType.inPerson:
        return Icons.local_hospital_outlined;
      case ConsultationType.videoCall:
        return Icons.videocam_outlined;
    }
  }

  static ConsultationType fromString(String? type) {
    switch (type?.toLowerCase()) {
      case 'online':
      case 'videocall':
      case 'video_call':
        return ConsultationType.videoCall;
      case 'in_person':
      case 'inperson':
      default:
        return ConsultationType.inPerson;
    }
  }
}

class AppointmentModel {
  final String id;
  final String doctorId;
  final String doctorName;
  final String doctorSpecialty;
  final String doctorAvatarUrl;
  final String clinicName;
  final String clinicAddress;
  final DateTime dateTime;
  final String timeSlot;
  final AppointmentStatus status;
  final ConsultationType consultationType;
  final double consultationFee;
  final double serviceFee;
  final double totalAmount;
  final String paymentMethod;
  final String? patientNotes;
  final String? diagnosisSummary;

  const AppointmentModel({
    required this.id,
    required this.doctorId,
    required this.doctorName,
    required this.doctorSpecialty,
    required this.doctorAvatarUrl,
    required this.clinicName,
    required this.clinicAddress,
    required this.dateTime,
    required this.timeSlot,
    required this.status,
    required this.consultationType,
    required this.consultationFee,
    required this.serviceFee,
    required this.totalAmount,
    required this.paymentMethod,
    this.patientNotes,
    this.diagnosisSummary,
  });

  factory AppointmentModel.fromJson(Map<String, dynamic> json) {
    final doctorData = json['doctor'];
    final String docName = json['doctor_name'] ??
        (doctorData is Map ? doctorData['name'] : '') ??
        'Doctor';
    final String docSpecialty = json['doctor_specialty'] ??
        (doctorData is Map ? doctorData['specialty_name'] : '') ??
        'Specialist';
    final String docAvatar = json['doctor_photo'] ??
        (doctorData is Map ? doctorData['avatar_url'] : null) ??
        'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80';

    DateTime parsedDate = DateTime.now();
    if (json['appointment_date'] != null) {
      try {
        parsedDate = DateTime.parse(json['appointment_date'].toString());
      } catch (_) {}
    }

    final rawTime = json['appointment_time']?.toString() ?? '10:00 AM';

    return AppointmentModel(
      id: json['id']?.toString() ?? '',
      doctorId: json['doctor_id']?.toString() ?? '',
      doctorName: docName,
      doctorSpecialty: docSpecialty,
      doctorAvatarUrl: docAvatar,
      clinicName: json['facility'] ?? 'Metropolitan Medical Center',
      clinicAddress: '742 Evergreen Terrace, Suite 402',
      dateTime: parsedDate,
      timeSlot: rawTime,
      status: AppointmentStatus.fromString(json['status']?.toString()),
      consultationType: ConsultationType.fromString(json['consultation_type']?.toString()),
      consultationFee: (json['consultation_fee'] as num?)?.toDouble() ?? 75.0,
      serviceFee: (json['service_fee'] as num?)?.toDouble() ?? 5.0,
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 80.0,
      paymentMethod: 'Credit Card',
      patientNotes: json['notes'],
      diagnosisSummary: null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'doctor_id': doctorId,
      'doctor_name': doctorName,
      'doctor_specialty': doctorSpecialty,
      'appointment_date': dateTime.toIso8601String(),
      'appointment_time': timeSlot,
      'status': status.name,
      'consultation_type': consultationType.name,
      'consultation_fee': consultationFee,
      'service_fee': serviceFee,
      'total_amount': totalAmount,
      'notes': patientNotes,
    };
  }

  AppointmentModel copyWith({
    String? id,
    String? doctorId,
    String? doctorName,
    String? doctorSpecialty,
    String? doctorAvatarUrl,
    String? clinicName,
    String? clinicAddress,
    DateTime? dateTime,
    String? timeSlot,
    AppointmentStatus? status,
    ConsultationType? consultationType,
    double? consultationFee,
    double? serviceFee,
    double? totalAmount,
    String? paymentMethod,
    String? patientNotes,
    String? diagnosisSummary,
  }) {
    return AppointmentModel(
      id: id ?? this.id,
      doctorId: doctorId ?? this.doctorId,
      doctorName: doctorName ?? this.doctorName,
      doctorSpecialty: doctorSpecialty ?? this.doctorSpecialty,
      doctorAvatarUrl: doctorAvatarUrl ?? this.doctorAvatarUrl,
      clinicName: clinicName ?? this.clinicName,
      clinicAddress: clinicAddress ?? this.clinicAddress,
      dateTime: dateTime ?? this.dateTime,
      timeSlot: timeSlot ?? this.timeSlot,
      status: status ?? this.status,
      consultationType: consultationType ?? this.consultationType,
      consultationFee: consultationFee ?? this.consultationFee,
      serviceFee: serviceFee ?? this.serviceFee,
      totalAmount: totalAmount ?? this.totalAmount,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      patientNotes: patientNotes ?? this.patientNotes,
      diagnosisSummary: diagnosisSummary ?? this.diagnosisSummary,
    );
  }
}
