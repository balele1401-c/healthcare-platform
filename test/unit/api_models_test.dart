import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/constants/app_constants.dart';
import 'package:healthcare/features/appointment/domain/models/appointment_model.dart';
import 'package:healthcare/features/auth/domain/models/user_model.dart';
import 'package:healthcare/features/chat/domain/models/chat_model.dart';
import 'package:healthcare/features/doctor/domain/models/doctor_model.dart';
import 'package:healthcare/features/notifications/domain/models/notification_model.dart';
import 'package:healthcare/features/prescriptions/domain/models/prescription_model.dart';
import 'package:healthcare/features/records/domain/models/medical_record_model.dart';

void main() {
  group('API Response JSON Parsing Tests', () {
    test('UserModel.fromJson parses Sanctum backend user payload', () {
      final json = {
        'id': 10,
        'name': 'Sarah Jenkins',
        'email': 'sarah@healthcare.local',
        'role': 'patient',
        'phone': '+15550192834',
        'avatar_url': 'https://example.com/avatar.jpg',
        'patient': {
          'id': 5,
          'blood_type': 'A+',
          'profile_photo': 'https://example.com/patient.jpg',
        },
      };

      final user = UserModel.fromJson(json);
      expect(user.id, '10');
      expect(user.name, 'Sarah Jenkins');
      expect(user.email, 'sarah@healthcare.local');
      expect(user.role, 'patient');
      expect(user.phoneNumber, '+15550192834');
      expect(user.isHealthProfileCompleted, true);
    });

    test('SpecialtyModel.fromJson parses specialty payload', () {
      final json = {
        'id': 1,
        'name': 'Cardiology',
        'slug': 'cardiology',
        'icon': 'favorite',
        'doctors_count': 6,
      };

      final specialty = SpecialtyModel.fromJson(json);
      expect(specialty.id, '1');
      expect(specialty.name, 'Cardiology');
      expect(specialty.iconName, 'favorite');
      expect(specialty.doctorCount, 6);
    });

    test('DoctorModel.fromJson parses doctor details and schedules', () {
      final json = {
        'id': 3,
        'name': 'Dr. Emily Chen',
        'specialty_name': 'Cardiology',
        'rating': 4.95,
        'review_count': 140,
        'experience_years': 12,
        'consultation_fee': 85.0,
        'facility': 'Metropolitan Medical Center',
        'biography': 'Senior cardiologist specializing in preventive medicine.',
        'education': 'Harvard Medical School',
        'schedules': [
          {'day_of_week': 1, 'start_time': '09:00:00', 'end_time': '12:00:00'},
          {'day_of_week': 3, 'start_time': '14:00:00', 'end_time': '17:00:00'},
        ],
      };

      final doctor = DoctorModel.fromJson(json);
      expect(doctor.id, '3');
      expect(doctor.name, 'Dr. Emily Chen');
      expect(doctor.specialty, 'Cardiology');
      expect(doctor.rating, 4.95);
      expect(doctor.consultationFee, 85.0);
      expect(doctor.availableDays.contains('Mon'), true);
      expect(doctor.availableDays.contains('Wed'), true);
    });

    test('AppointmentModel.fromJson parses appointment booking', () {
      final json = {
        'id': 12,
        'booking_code': 'APT-2026-98214',
        'doctor_id': 3,
        'doctor_name': 'Dr. Emily Chen',
        'doctor_specialty': 'Cardiology',
        'appointment_date': '2026-08-25',
        'appointment_time': '10:30:00',
        'status': 'confirmed',
        'consultation_type': 'online',
        'consultation_fee': 75.0,
        'service_fee': 5.0,
        'total_amount': 80.0,
        'notes': 'Follow-up consultation',
      };

      final apt = AppointmentModel.fromJson(json);
      expect(apt.id, '12');
      expect(apt.doctorName, 'Dr. Emily Chen');
      expect(apt.status, AppointmentStatus.upcoming);
      expect(apt.consultationType, ConsultationType.videoCall);
      expect(apt.totalAmount, 80.0);
      expect(apt.patientNotes, 'Follow-up consultation');
    });

    test('MedicalRecordModel.fromJson parses clinical records and vitals', () {
      final json = {
        'id': 4,
        'record_number': 'REC-2026-004',
        'visit_date': '2026-08-10',
        'doctor_name': 'Dr. Sophia Rodriguez',
        'doctor_specialty': 'Neurology',
        'chief_complaint': 'Tension headache',
        'symptoms': 'Throbbing pain, Mild photophobia',
        'diagnosis': 'Episodic tension headache',
        'treatment': 'Magnesium glycinate 400mg',
        'clinical_notes': 'Maintain sleep routine',
        'vital_signs': {
          'blood_pressure_formatted': '118/76 mmHg',
          'heart_rate': 72,
          'body_temperature': 36.6,
          'weight': 64.5,
          'height': 168.0,
          'blood_oxygen': 99,
        },
      };

      final record = MedicalRecordModel.fromJson(json);
      expect(record.id, '4');
      expect(record.doctorName, 'Dr. Sophia Rodriguez');
      expect(record.diagnosis, 'Episodic tension headache');
      expect(record.vitals.bloodPressure, '118/76 mmHg');
      expect(record.vitals.heartRateBpm, 72);
      expect(record.symptoms.length, 2);
    });

    test('PrescriptionModel.fromJson parses prescription with line items', () {
      final json = {
        'id': 8,
        'prescription_code': 'RX-2026-008',
        'doctor_name': 'Dr. Emily Chen',
        'prescription_date': '2026-08-12',
        'status': 'active',
        'items': [
          {
            'medicine_name': 'Amlodipine Besylate',
            'dosage': '5mg',
            'dosage_form': 'Tablet',
            'frequency': 'Once daily',
            'duration': '30 Days',
            'quantity': 30,
            'refills_available': 2,
          }
        ],
      };

      final prescription = PrescriptionModel.fromJson(json);
      expect(prescription.id, '8');
      expect(prescription.status, PrescriptionStatus.active);
      expect(prescription.items.length, 1);
      expect(prescription.items.first.medicineName, 'Amlodipine Besylate');
      expect(prescription.items.first.refillsRemaining, 2);
    });

    test('NotificationModel.fromJson parses notification alerts', () {
      final json = {
        'id': 15,
        'title': 'Appointment Confirmed',
        'message': 'Your appointment with Dr. Chen is confirmed for tomorrow.',
        'notification_type': 'appointment_reminder',
        'is_read': false,
        'created_at': '2026-08-19T10:00:00Z',
      };

      final notif = NotificationModel.fromJson(json);
      expect(notif.id, '15');
      expect(notif.title, 'Appointment Confirmed');
      expect(notif.category, NotificationCategory.appointment);
      expect(notif.isRead, false);
    });

    test('ChatMessage.fromJson parses chat consultation messages', () {
      final json = {
        'id': 42,
        'conversation_id': 2,
        'sender_id': 5,
        'sender_role': 'patient',
        'message': 'Hello doctor, my blood pressure reading is 118/76.',
        'is_mine': true,
        'created_at': '2026-08-19T15:30:00Z',
      };

      final message = ChatMessage.fromJson(json);
      expect(message.id, '42');
      expect(message.content, 'Hello doctor, my blood pressure reading is 118/76.');
      expect(message.sender, MessageSender.patient);
    });
  });

  group('AppConstants URL Configuration Tests', () {
    test('AppConstants.baseApiUrl resolves default Railway API without error', () {
      final url = AppConstants.baseApiUrl;
      expect(url.contains('/api/v1'), true);
      expect(url.startsWith('https://'), true);
      expect(url, 'https://healthcare-platform-production-5197.up.railway.app/api/v1');
    });

    test('AppConstants customBaseApiUrl override takes precedence', () {
      AppConstants.customBaseApiUrl = 'http://192.168.1.100:8000/api/v1';
      expect(AppConstants.baseApiUrl, 'http://192.168.1.100:8000/api/v1');
      AppConstants.customBaseApiUrl = null; // Reset
    });
  });
}
