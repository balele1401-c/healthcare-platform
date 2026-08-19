import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/appointment/data/repositories/mock_appointment_repository.dart';
import 'package:healthcare/features/appointment/domain/models/appointment_model.dart';

void main() {
  late MockAppointmentRepository repository;

  setUp(() {
    repository = MockAppointmentRepository();
  });

  group('MockAppointmentRepository Tests', () {
    test('getAppointments returns initial appointment list', () async {
      final appointments = await repository.getAppointments();
      expect(appointments.isNotEmpty, true);
    });

    test('getAppointments with status returns only filtered appointments', () async {
      final upcoming = await repository.getAppointments(status: AppointmentStatus.upcoming);
      expect(upcoming.every((a) => a.status == AppointmentStatus.upcoming), true);
    });

    test('createAppointment adds new appointment with valid ID', () async {
      final newAppt = await repository.createAppointment(
        doctorId: 'doc_1',
        doctorName: 'Dr. Emily Chen',
        doctorSpecialty: 'Senior Cardiologist',
        doctorAvatarUrl: '',
        clinicName: 'Metropolitan Clinic',
        clinicAddress: '742 Evergreen Terrace',
        dateTime: DateTime.now().add(const Duration(days: 3)),
        timeSlot: '11:00 AM',
        consultationType: ConsultationType.videoCall,
        consultationFee: 75.0,
        paymentMethod: 'Credit Card',
        patientNotes: 'Routine cardiology follow-up',
      );

      expect(newAppt.id.startsWith('APT-'), true);
      expect(newAppt.doctorName, 'Dr. Emily Chen');
      expect(newAppt.status, AppointmentStatus.upcoming);

      final upcoming = await repository.getAppointments(status: AppointmentStatus.upcoming);
      expect(upcoming.any((a) => a.id == newAppt.id), true);
    });

    test('cancelAppointment updates status to cancelled', () async {
      final upcoming = await repository.getAppointments(status: AppointmentStatus.upcoming);
      final targetId = upcoming.first.id;

      final success = await repository.cancelAppointment(targetId);
      expect(success, true);

      final cancelled = await repository.getAppointments(status: AppointmentStatus.cancelled);
      expect(cancelled.any((a) => a.id == targetId), true);
    });

    test('rescheduleAppointment updates appointment date and slot', () async {
      final upcoming = await repository.getAppointments(status: AppointmentStatus.upcoming);
      final targetId = upcoming.first.id;
      final newDate = DateTime.now().add(const Duration(days: 7));

      final success = await repository.rescheduleAppointment(targetId, newDate, '03:30 PM');
      expect(success, true);

      final updated = await repository.getAppointmentById(targetId);
      expect(updated!.timeSlot, '03:30 PM');
    });
  });
}
