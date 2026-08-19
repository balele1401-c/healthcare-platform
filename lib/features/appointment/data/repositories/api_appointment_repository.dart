import 'package:intl/intl.dart';
import '../../../../core/network/api_client.dart';
import '../../domain/models/appointment_model.dart';
import '../../domain/repositories/appointment_repository.dart';

class ApiAppointmentRepository implements AppointmentRepository {
  final ApiClient _apiClient;

  ApiAppointmentRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<AppointmentModel>> getAppointments({AppointmentStatus? status}) async {
    final queryParams = <String, dynamic>{};
    if (status != null) {
      queryParams['status'] = status == AppointmentStatus.upcoming ? 'confirmed' : status.name;
    }

    final response = await _apiClient.get('/patient/appointments', queryParameters: queryParams);
    final data = response.data['data'] as List? ?? [];

    return data
        .map((item) => AppointmentModel.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  @override
  Future<AppointmentModel?> getNextUpcomingAppointment() async {
    final appointments = await getAppointments(status: AppointmentStatus.upcoming);
    if (appointments.isEmpty) {
      final all = await getAppointments();
      final upcoming = all.where((a) => a.status == AppointmentStatus.upcoming).toList();
      if (upcoming.isEmpty) return null;
      upcoming.sort((a, b) => a.dateTime.compareTo(b.dateTime));
      return upcoming.first;
    }
    appointments.sort((a, b) => a.dateTime.compareTo(b.dateTime));
    return appointments.first;
  }

  @override
  Future<AppointmentModel?> getAppointmentById(String id) async {
    try {
      final response = await _apiClient.get('/appointments/$id');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        return AppointmentModel.fromJson(data);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  @override
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
  }) async {
    final dateStr = DateFormat('yyyy-MM-dd').format(dateTime);

    // Format time slot to 24-hour H:i (e.g. 10:30 AM -> 10:30, 02:00 PM -> 14:00)
    String formattedTime = timeSlot.replaceAll(RegExp(r'[^\d:]'), '');
    if (timeSlot.toUpperCase().contains('PM')) {
      final parts = formattedTime.split(':');
      if (parts.isNotEmpty) {
        int hour = int.tryParse(parts[0]) ?? 12;
        if (hour < 12) hour += 12;
        formattedTime = '${hour.toString().padLeft(2, '0')}:${parts.length > 1 ? parts[1] : '00'}';
      }
    } else if (timeSlot.toUpperCase().contains('AM')) {
      final parts = formattedTime.split(':');
      if (parts.isNotEmpty) {
        int hour = int.tryParse(parts[0]) ?? 9;
        if (hour == 12) hour = 0;
        formattedTime = '${hour.toString().padLeft(2, '0')}:${parts.length > 1 ? parts[1] : '00'}';
      }
    }

    if (!formattedTime.contains(':')) {
      formattedTime = '10:00';
    }

    final response = await _apiClient.post('/appointments', data: {
      'doctor_id': doctorId,
      'appointment_date': dateStr,
      'appointment_time': formattedTime,
      'consultation_type': consultationType == ConsultationType.videoCall ? 'online' : 'in_person',
      'facility': clinicName,
      'notes': patientNotes,
    });

    final data = response.data['data'] as Map<String, dynamic>;
    return AppointmentModel.fromJson(data);
  }

  @override
  Future<bool> cancelAppointment(String id) async {
    try {
      await _apiClient.post('/appointments/$id/cancel', data: {
        'cancellation_reason': 'Cancelled by patient from mobile app.',
      });
      return true;
    } catch (_) {
      return false;
    }
  }

  @override
  Future<bool> rescheduleAppointment(String id, DateTime newDate, String newTimeSlot) async {
    try {
      final dateStr = DateFormat('yyyy-MM-dd').format(newDate);
      await _apiClient.put('/appointments/$id', data: {
        'appointment_date': dateStr,
        'appointment_time': newTimeSlot,
      });
      return true;
    } catch (_) {
      return false;
    }
  }
}
