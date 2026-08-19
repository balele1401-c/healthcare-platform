/// Route path constants for GoRouter.
abstract class AppRoutes {
  static const String splash = '/splash';
  static const String onboarding = '/onboarding';
  static const String login = '/login';
  static const String register = '/register';
  static const String forgotPassword = '/forgot-password';
  static const String otp = '/otp';
  static const String createHealthProfile = '/create-health-profile';
  static const String home = '/home';

  // Doctor Discovery
  static const String doctorSearch = '/doctor-search';
  static const String doctorDetail = '/doctor-detail';

  // Appointment Booking Flow
  static const String selectDateTime = '/select-date-time';
  static const String appointmentConfirmation = '/appointment-confirmation';
  static const String payment = '/payment';
  static const String appointmentSuccess = '/appointment-success';

  // Appointments Management
  static const String myAppointments = '/my-appointments';
  static const String appointmentDetail = '/appointment-detail';

  // Medical Records & Prescriptions
  static const String medicalRecords = '/medical-records';
  static const String medicalRecordDetail = '/medical-record-detail';
  static const String prescriptions = '/prescriptions';
  static const String prescriptionDetail = '/prescription-detail';

  // Health Tracker
  static const String healthTracker = '/health-tracker';
  static const String healthMetricDetail = '/health-metric-detail';

  // Notifications
  static const String notifications = '/notifications';

  // Profile & Settings
  static const String profile = '/profile';
  static const String editProfile = '/edit-profile';
  static const String settings = '/settings';

  // Chat & AI Assistant
  static const String chat = '/chat';
  static const String chatDetail = '/chat-detail';
  static const String aiAssistant = '/ai-assistant';
}
