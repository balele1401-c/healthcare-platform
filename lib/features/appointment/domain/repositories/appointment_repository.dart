import '../models/appointment_model.dart';

abstract class AppointmentRepository {
  Future<List<AppointmentModel>> getAppointments({AppointmentStatus? status});

  Future<AppointmentModel?> getNextUpcomingAppointment();

  Future<AppointmentModel?> getAppointmentById(String id);

  Future<AppointmentModel> createAppointment({
    required String doctorId,
    required String doctorName,
    required String doctorSpecialty,
    required String doctorAvatarUrl,
    required String clinicName,
    required String clinicAddress,
    required DateTime dateTime,
    required String timeSlot,
    required ConsultationType consultationType,
    required double consultationFee,
    required String paymentMethod,
    String? patientNotes,
  });

  Future<bool> cancelAppointment(String id);

  Future<bool> rescheduleAppointment(String id, DateTime newDate, String newTimeSlot);
}
