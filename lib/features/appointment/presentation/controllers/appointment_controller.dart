import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../../doctor/domain/models/doctor_model.dart';
import '../../data/repositories/api_appointment_repository.dart';
import '../../domain/models/appointment_model.dart';
import '../../domain/repositories/appointment_repository.dart';

final appointmentRepositoryProvider = Provider<AppointmentRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiAppointmentRepository(apiClient: apiClient);
});

class BookingDraftState {
  final DoctorModel? doctor;
  final DateTime? selectedDate;
  final String? selectedTimeSlot;
  final ConsultationType consultationType;
  final String selectedPaymentMethod;
  final String? patientNotes;

  const BookingDraftState({
    this.doctor,
    this.selectedDate,
    this.selectedTimeSlot,
    this.consultationType = ConsultationType.inPerson,
    this.selectedPaymentMethod = 'Credit Card (•••• 4242)',
    this.patientNotes,
  });

  BookingDraftState copyWith({
    DoctorModel? doctor,
    DateTime? selectedDate,
    String? selectedTimeSlot,
    ConsultationType? consultationType,
    String? selectedPaymentMethod,
    String? patientNotes,
  }) {
    return BookingDraftState(
      doctor: doctor ?? this.doctor,
      selectedDate: selectedDate ?? this.selectedDate,
      selectedTimeSlot: selectedTimeSlot ?? this.selectedTimeSlot,
      consultationType: consultationType ?? this.consultationType,
      selectedPaymentMethod: selectedPaymentMethod ?? this.selectedPaymentMethod,
      patientNotes: patientNotes ?? this.patientNotes,
    );
  }
}

class BookingDraftNotifier extends StateNotifier<BookingDraftState> {
  BookingDraftNotifier() : super(const BookingDraftState());

  void setDoctor(DoctorModel doctor) {
    state = state.copyWith(doctor: doctor);
  }

  void setDate(DateTime date) {
    state = state.copyWith(selectedDate: date);
  }

  void setTimeSlot(String slot) {
    state = state.copyWith(selectedTimeSlot: slot);
  }

  void setConsultationType(ConsultationType type) {
    state = state.copyWith(consultationType: type);
  }

  void setPaymentMethod(String method) {
    state = state.copyWith(selectedPaymentMethod: method);
  }

  void setPatientNotes(String notes) {
    state = state.copyWith(patientNotes: notes);
  }

  void reset() {
    state = const BookingDraftState();
  }
}

final bookingDraftProvider = StateNotifierProvider<BookingDraftNotifier, BookingDraftState>((ref) {
  return BookingDraftNotifier();
});

final upcomingAppointmentsProvider = FutureProvider<List<AppointmentModel>>((ref) async {
  final repository = ref.watch(appointmentRepositoryProvider);
  return repository.getAppointments(status: AppointmentStatus.upcoming);
});

final completedAppointmentsProvider = FutureProvider<List<AppointmentModel>>((ref) async {
  final repository = ref.watch(appointmentRepositoryProvider);
  return repository.getAppointments(status: AppointmentStatus.completed);
});

final cancelledAppointmentsProvider = FutureProvider<List<AppointmentModel>>((ref) async {
  final repository = ref.watch(appointmentRepositoryProvider);
  return repository.getAppointments(status: AppointmentStatus.cancelled);
});

final nextAppointmentProvider = FutureProvider<AppointmentModel?>((ref) async {
  final repository = ref.watch(appointmentRepositoryProvider);
  return repository.getNextUpcomingAppointment();
});

final appointmentDetailProvider = FutureProvider.family<AppointmentModel?, String>((ref, id) async {
  final repository = ref.watch(appointmentRepositoryProvider);
  return repository.getAppointmentById(id);
});
