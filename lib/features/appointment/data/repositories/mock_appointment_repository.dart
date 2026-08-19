import '../../domain/models/appointment_model.dart';
import '../../domain/repositories/appointment_repository.dart';

class MockAppointmentRepository implements AppointmentRepository {
  static final List<AppointmentModel> _appointments = [
    AppointmentModel(
      id: 'APT-98214',
      doctorId: 'doc_1',
      doctorName: 'Dr. Emily Chen',
      doctorSpecialty: 'Senior Cardiologist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      clinicName: 'Metropolitan Heart & Vascular Institute',
      clinicAddress: '742 Evergreen Terrace, Medical District, Suite 400',
      dateTime: DateTime.now().add(const Duration(hours: 3)),
      timeSlot: '10:30 AM',
      status: AppointmentStatus.upcoming,
      consultationType: ConsultationType.videoCall,
      consultationFee: 75.0,
      serviceFee: 5.0,
      totalAmount: 80.0,
      paymentMethod: 'Credit Card (•••• 4242)',
      patientNotes: 'Routine post-medication follow-up and blood pressure review.',
      diagnosisSummary: 'Stable sinus rhythm with mild hypertension under control.',
    ),
    AppointmentModel(
      id: 'APT-97451',
      doctorId: 'doc_2',
      doctorName: 'Dr. Marcus Vance',
      doctorSpecialty: 'Consultant Dermatologist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80',
      clinicName: 'Clarity Dermatology & Laser Clinic',
      clinicAddress: '108 West Coast Highway, Suite 210',
      dateTime: DateTime.now().add(const Duration(days: 4, hours: 2)),
      timeSlot: '02:00 PM',
      status: AppointmentStatus.upcoming,
      consultationType: ConsultationType.inPerson,
      consultationFee: 65.0,
      serviceFee: 5.0,
      totalAmount: 70.0,
      paymentMethod: 'Bank Virtual Account',
      patientNotes: 'Skin check on forearm rash.',
    ),
    AppointmentModel(
      id: 'APT-84192',
      doctorId: 'doc_3',
      doctorName: 'Dr. Sophia Rodriguez',
      doctorSpecialty: 'Neurologist & Sleep Specialist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1594824813591-28c9b33a5712?auto=format&fit=crop&w=400&q=80',
      clinicName: 'NeuroHealth Comprehensive Care Center',
      clinicAddress: '350 5th Avenue, Suite 1200',
      dateTime: DateTime.now().subtract(const Duration(days: 14)),
      timeSlot: '11:00 AM',
      status: AppointmentStatus.completed,
      consultationType: ConsultationType.inPerson,
      consultationFee: 90.0,
      serviceFee: 5.0,
      totalAmount: 95.0,
      paymentMethod: 'E-Wallet (QRIS)',
      patientNotes: 'Tension headache consult.',
      diagnosisSummary: 'Episodic tension-type headache. Prescribed magnesium supplementation and sleep routine adjustments.',
    ),
    AppointmentModel(
      id: 'APT-71829',
      doctorId: 'doc_6',
      doctorName: 'Dr. James Wilson',
      doctorSpecialty: 'Family Medicine Physician',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=400&q=80',
      clinicName: 'Community Health Partners',
      clinicAddress: '220 Pine Street, Central Wing',
      dateTime: DateTime.now().subtract(const Duration(days: 45)),
      timeSlot: '09:30 AM',
      status: AppointmentStatus.completed,
      consultationType: ConsultationType.inPerson,
      consultationFee: 50.0,
      serviceFee: 5.0,
      totalAmount: 55.0,
      paymentMethod: 'Debit Card (•••• 1089)',
      patientNotes: 'Annual comprehensive physical screening.',
      diagnosisSummary: 'Healthy adult examination. Vitals within normal limits.',
    ),
    AppointmentModel(
      id: 'APT-60912',
      doctorId: 'doc_4',
      doctorName: 'Dr. David Kim',
      doctorSpecialty: 'Pediatric Specialist',
      doctorAvatarUrl: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=400&q=80',
      clinicName: 'Sunrise Pediatrics Center',
      clinicAddress: '88 Bloom Street, Floor 2',
      dateTime: DateTime.now().subtract(const Duration(days: 60)),
      timeSlot: '04:00 PM',
      status: AppointmentStatus.cancelled,
      consultationType: ConsultationType.videoCall,
      consultationFee: 55.0,
      serviceFee: 5.0,
      totalAmount: 60.0,
      paymentMethod: 'Bank Transfer',
      patientNotes: 'Cancelled by patient due to scheduling conflict.',
    ),
  ];

  @override
  Future<List<AppointmentModel>> getAppointments({AppointmentStatus? status}) async {
    await Future.delayed(const Duration(milliseconds: 250));
    if (status == null) {
      return List.unmodifiable(_appointments);
    }
    return _appointments.where((a) => a.status == status).toList();
  }

  @override
  Future<AppointmentModel?> getNextUpcomingAppointment() async {
    await Future.delayed(const Duration(milliseconds: 200));
    final upcoming = _appointments.where((a) => a.status == AppointmentStatus.upcoming).toList();
    if (upcoming.isEmpty) return null;
    upcoming.sort((a, b) => a.dateTime.compareTo(b.dateTime));
    return upcoming.first;
  }

  @override
  Future<AppointmentModel?> getAppointmentById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _appointments.firstWhere((a) => a.id == id);
    } catch (_) {
      return _appointments.first;
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
    await Future.delayed(const Duration(milliseconds: 400));

    final newApt = AppointmentModel(
      id: 'APT-${DateTime.now().millisecondsSinceEpoch.toString().substring(7)}',
      doctorId: doctorId,
      doctorName: doctorName,
      doctorSpecialty: doctorSpecialty,
      doctorAvatarUrl: doctorAvatarUrl,
      clinicName: clinicName,
      clinicAddress: clinicAddress,
      dateTime: dateTime,
      timeSlot: timeSlot,
      status: AppointmentStatus.upcoming,
      consultationType: consultationType,
      consultationFee: consultationFee,
      serviceFee: 5.0,
      totalAmount: consultationFee + 5.0,
      paymentMethod: paymentMethod,
      patientNotes: patientNotes,
    );

    _appointments.insert(0, newApt);
    return newApt;
  }

  @override
  Future<bool> cancelAppointment(String id) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final index = _appointments.indexWhere((a) => a.id == id);
    if (index != -1) {
      _appointments[index] = _appointments[index].copyWith(status: AppointmentStatus.cancelled);
      return true;
    }
    return false;
  }

  @override
  Future<bool> rescheduleAppointment(String id, DateTime newDate, String newTimeSlot) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final index = _appointments.indexWhere((a) => a.id == id);
    if (index != -1) {
      _appointments[index] = _appointments[index].copyWith(
        dateTime: newDate,
        timeSlot: newTimeSlot,
        status: AppointmentStatus.upcoming,
      );
      return true;
    }
    return false;
  }
}
