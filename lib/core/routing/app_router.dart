import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../features/ai_assistant/presentation/views/ai_assistant_screen.dart';
import '../../features/appointment/domain/models/appointment_model.dart';
import '../../features/appointment/presentation/views/appointment_confirmation_screen.dart';
import '../../features/appointment/presentation/views/appointment_detail_screen.dart';
import '../../features/appointment/presentation/views/appointment_success_screen.dart';
import '../../features/appointment/presentation/views/my_appointments_screen.dart';
import '../../features/appointment/presentation/views/payment_screen.dart';
import '../../features/appointment/presentation/views/select_date_time_screen.dart';
import '../../features/auth/presentation/views/create_health_profile_screen.dart';
import '../../features/auth/presentation/views/forgot_password_screen.dart';
import '../../features/auth/presentation/views/onboarding_screen.dart';
import '../../features/auth/presentation/views/otp_verification_screen.dart';
import '../../features/auth/presentation/views/patient_login_screen.dart';
import '../../features/auth/presentation/views/patient_register_screen.dart';
import '../../features/auth/presentation/views/splash_screen.dart';
import '../../features/chat/domain/models/chat_model.dart';
import '../../features/chat/presentation/views/conversation_list_screen.dart';
import '../../features/chat/presentation/views/doctor_chat_screen.dart';
import '../../features/doctor/domain/models/doctor_model.dart';
import '../../features/doctor/presentation/views/doctor_detail_screen.dart';
import '../../features/doctor/presentation/views/doctor_search_screen.dart';
import '../../features/health_tracker/domain/models/health_metric_model.dart';
import '../../features/health_tracker/presentation/views/health_metric_detail_screen.dart';
import '../../features/health_tracker/presentation/views/health_tracker_screen.dart';
import '../../features/home/presentation/views/main_shell_screen.dart';
import '../../features/notifications/presentation/views/notifications_screen.dart';
import '../../features/prescriptions/domain/models/prescription_model.dart';
import '../../features/prescriptions/presentation/views/prescription_detail_screen.dart';
import '../../features/prescriptions/presentation/views/prescriptions_screen.dart';
import '../../features/profile/presentation/views/edit_profile_screen.dart';
import '../../features/profile/presentation/views/profile_screen.dart';
import '../../features/profile/presentation/views/settings_screen.dart';
import '../../features/records/domain/models/medical_record_model.dart';
import '../../features/records/presentation/views/medical_record_detail_screen.dart';
import '../../features/records/presentation/views/medical_records_screen.dart';
import 'app_routes.dart';

abstract class AppRouter {
  static final GlobalKey<NavigatorState> rootNavigatorKey = GlobalKey<NavigatorState>(debugLabel: 'root');

  static final GoRouter router = GoRouter(
    navigatorKey: rootNavigatorKey,
    initialLocation: AppRoutes.splash,
    debugLogDiagnostics: false,
    routes: [
      // 1. Auth & Onboarding Flow
      GoRoute(
        path: AppRoutes.splash,
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: AppRoutes.onboarding,
        builder: (context, state) => const OnboardingScreen(),
      ),
      GoRoute(
        path: AppRoutes.login,
        builder: (context, state) => const PatientLoginScreen(),
      ),
      GoRoute(
        path: AppRoutes.register,
        builder: (context, state) => const PatientRegisterScreen(),
      ),
      GoRoute(
        path: AppRoutes.forgotPassword,
        builder: (context, state) => const ForgotPasswordScreen(),
      ),
      GoRoute(
        path: AppRoutes.otp,
        builder: (context, state) => const OtpVerificationScreen(),
      ),
      GoRoute(
        path: AppRoutes.createHealthProfile,
        builder: (context, state) => const CreateHealthProfileScreen(),
      ),

      // 2. Home Navigation Shell
      GoRoute(
        path: AppRoutes.home,
        builder: (context, state) => const MainShellScreen(),
      ),

      // 3. Doctor Discovery
      GoRoute(
        path: AppRoutes.doctorSearch,
        builder: (context, state) => const DoctorSearchScreen(),
      ),
      GoRoute(
        path: AppRoutes.doctorDetail,
        builder: (context, state) {
          final doctor = state.extra as DoctorModel? ??
              const DoctorModel(
                id: 'doc_1',
                name: 'Dr. Emily Chen',
                specialty: 'Senior Cardiologist',
                specialtyId: 'cardiology',
                title: 'MD, FACC - Cardiovascular Specialist',
                rating: 4.9,
                reviewCount: 128,
                experienceYears: 12,
                patientCount: 2450,
                consultationFee: 75.0,
                avatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                biography: 'Dr. Emily Chen is a board-certified Senior Cardiologist.',
                education: 'Harvard Medical School (MD)',
                clinicName: 'Metropolitan Heart & Vascular Institute',
                clinicAddress: '742 Evergreen Terrace, Medical District, Suite 400',
              );
          return DoctorDetailScreen(doctor: doctor);
        },
      ),

      // 4. Booking Flow
      GoRoute(
        path: AppRoutes.selectDateTime,
        builder: (context, state) {
          final doctor = state.extra as DoctorModel? ??
              const DoctorModel(
                id: 'doc_1',
                name: 'Dr. Emily Chen',
                specialty: 'Senior Cardiologist',
                specialtyId: 'cardiology',
                title: 'MD, FACC',
                rating: 4.9,
                reviewCount: 128,
                experienceYears: 12,
                patientCount: 2450,
                consultationFee: 75.0,
                avatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                biography: 'Cardiology Specialist',
                education: 'Harvard Medical School',
                clinicName: 'Metropolitan Heart & Vascular Institute',
                clinicAddress: '742 Evergreen Terrace',
              );
          return SelectDateTimeScreen(doctor: doctor);
        },
      ),
      GoRoute(
        path: AppRoutes.appointmentConfirmation,
        builder: (context, state) => const AppointmentConfirmationScreen(),
      ),
      GoRoute(
        path: AppRoutes.payment,
        builder: (context, state) => const PaymentScreen(),
      ),
      GoRoute(
        path: AppRoutes.appointmentSuccess,
        builder: (context, state) {
          final appointment = state.extra as AppointmentModel? ??
              AppointmentModel(
                id: 'APT-98214',
                doctorId: 'doc_1',
                doctorName: 'Dr. Emily Chen',
                doctorSpecialty: 'Senior Cardiologist',
                doctorAvatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                clinicName: 'Metropolitan Heart & Vascular Institute',
                clinicAddress: '742 Evergreen Terrace',
                dateTime: DateTime.now().add(const Duration(days: 1)),
                timeSlot: '10:30 AM',
                status: AppointmentStatus.upcoming,
                consultationType: ConsultationType.videoCall,
                consultationFee: 75.0,
                serviceFee: 5.0,
                totalAmount: 80.0,
                paymentMethod: 'Credit Card',
              );
          return AppointmentSuccessScreen(appointment: appointment);
        },
      ),

      // 5. Appointments
      GoRoute(
        path: AppRoutes.myAppointments,
        builder: (context, state) => const MyAppointmentsScreen(),
      ),
      GoRoute(
        path: AppRoutes.appointmentDetail,
        builder: (context, state) {
          final appointment = state.extra as AppointmentModel? ??
              AppointmentModel(
                id: 'APT-98214',
                doctorId: 'doc_1',
                doctorName: 'Dr. Emily Chen',
                doctorSpecialty: 'Senior Cardiologist',
                doctorAvatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                clinicName: 'Metropolitan Heart & Vascular Institute',
                clinicAddress: '742 Evergreen Terrace',
                dateTime: DateTime.now().add(const Duration(hours: 3)),
                timeSlot: '10:30 AM',
                status: AppointmentStatus.upcoming,
                consultationType: ConsultationType.videoCall,
                consultationFee: 75.0,
                serviceFee: 5.0,
                totalAmount: 80.0,
                paymentMethod: 'Credit Card',
              );
          return AppointmentDetailScreen(appointment: appointment);
        },
      ),

      // 6. Medical Records
      GoRoute(
        path: AppRoutes.medicalRecords,
        builder: (context, state) => const MedicalRecordsScreen(),
      ),
      GoRoute(
        path: AppRoutes.medicalRecordDetail,
        builder: (context, state) {
          final record = state.extra as MedicalRecordModel? ??
              MedicalRecordModel(
                id: 'REC-2024-001',
                visitDate: DateTime.now().subtract(const Duration(days: 14)),
                doctorName: 'Dr. Sophia Rodriguez',
                doctorSpecialty: 'Neurologist',
                clinicName: 'NeuroHealth Comprehensive Care Center',
                chiefComplaint: 'Frequent bilateral frontal tension headaches.',
                symptoms: const ['Mild photophobia', 'Neck muscle tightness'],
                vitals: const VitalSignsModel(
                  bloodPressure: '118/76',
                  heartRateBpm: 72,
                  bodyTempCelsius: 36.6,
                  weightKg: 64.5,
                  heightCm: 168.0,
                  oxygenSaturationPercent: 99,
                ),
                diagnosis: 'Episodic tension-type headache (G44.2).',
                treatmentPlan: 'Magnesium glycinate 400mg daily.',
                followUpInstructions: 'Return in 6 weeks if symptoms persist.',
              );
          return MedicalRecordDetailScreen(record: record);
        },
      ),

      // 7. Prescriptions
      GoRoute(
        path: AppRoutes.prescriptions,
        builder: (context, state) => const PrescriptionsScreen(),
      ),
      GoRoute(
        path: AppRoutes.prescriptionDetail,
        builder: (context, state) {
          final prescription = state.extra as PrescriptionModel? ??
              PrescriptionModel(
                id: 'RX-88291',
                doctorName: 'Dr. Emily Chen',
                doctorSpecialty: 'Senior Cardiologist',
                issuedDate: DateTime.now().subtract(const Duration(days: 5)),
                validUntil: DateTime.now().add(const Duration(days: 85)),
                status: PrescriptionStatus.active,
                notes: 'Take daily in morning.',
                items: const [
                  PrescriptionItemModel(
                    medicineName: 'Amlodipine Besylate',
                    dosage: '5 mg',
                    form: 'Oral Tablet',
                    frequency: 'Once Daily (Morning)',
                    duration: '90 Days',
                    instructions: 'Take with or without food.',
                    totalQuantity: 90,
                  ),
                ],
              );
          return PrescriptionDetailScreen(prescription: prescription);
        },
      ),

      // 8. Health Tracker
      GoRoute(
        path: AppRoutes.healthTracker,
        builder: (context, state) => const HealthTrackerScreen(),
      ),
      GoRoute(
        path: AppRoutes.healthMetricDetail,
        builder: (context, state) {
          final metric = state.extra as HealthMetricModel? ??
              HealthMetricModel(
                type: HealthMetricType.heartRate,
                currentValue: '72',
                statusLabel: 'Resting • Normal',
                trend: 'Avg 68-74 bpm this week',
                lastUpdated: DateTime.now(),
                history: [
                  MetricReading(timestamp: DateTime.now(), value: 72),
                ],
              );
          return HealthMetricDetailScreen(metric: metric);
        },
      ),

      // 9. Notifications
      GoRoute(
        path: AppRoutes.notifications,
        builder: (context, state) => const NotificationsScreen(),
      ),

      // 10. Profile & Settings
      GoRoute(
        path: AppRoutes.profile,
        builder: (context, state) => const ProfileScreen(),
      ),
      GoRoute(
        path: AppRoutes.editProfile,
        builder: (context, state) => const EditProfileScreen(),
      ),
      GoRoute(
        path: AppRoutes.settings,
        builder: (context, state) => const SettingsScreen(),
      ),

      // 11. Chat & AI Assistant
      GoRoute(
        path: AppRoutes.chat,
        builder: (context, state) => const ConversationListScreen(),
      ),
      GoRoute(
        path: AppRoutes.chatDetail,
        builder: (context, state) {
          final conv = state.extra as ChatConversation? ??
              ChatConversation(
                id: 'chat_1',
                doctorId: 'doc_1',
                doctorName: 'Dr. Emily Chen',
                doctorSpecialty: 'Senior Cardiologist',
                doctorAvatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                isDoctorOnline: true,
                lastMessage: 'Good morning Sarah, please upload your latest home BP log.',
                lastMessageTime: DateTime.now().subtract(const Duration(minutes: 42)),
                unreadCount: 1,
                messages: [
                  ChatMessage(
                    id: 'm1',
                    senderId: 'doc_1',
                    sender: MessageSender.doctor,
                    content: 'Hello Sarah! How have you been feeling on the new dosage?',
                    timestamp: DateTime.now().subtract(const Duration(hours: 2)),
                  ),
                ],
              );
          return DoctorChatScreen(conversation: conv);
        },
      ),
      GoRoute(
        path: AppRoutes.aiAssistant,
        builder: (context, state) => const AIAssistantScreen(),
      ),
    ],
  );
}
