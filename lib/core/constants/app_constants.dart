import '../config/api_config.dart';

/// Application-wide constants and network environment configuration.
abstract class AppConstants {
  static const String appName = 'HealthCare';
  static const String appTagline = 'Your Health, Our Priority';
  static const String appVersion = '1.0.0';

  // Storage Keys
  static const String tokenKey = 'healthcare_auth_token';
  static const String refreshTokenKey = 'healthcare_refresh_token';
  static const String userKey = 'healthcare_user_data';
  static const String onboardingCompletedKey = 'healthcare_onboarding_completed';

  /// Centralized Base URL for Laravel 12 API on Railway
  static String get baseApiUrl => ApiConfig.baseUrl;

  static set customBaseApiUrl(String? url) {
    ApiConfig.customBaseUrl = url;
  }

  static String? get customBaseApiUrl => null;

  static Duration get connectTimeout => ApiConfig.connectTimeout;
  static Duration get receiveTimeout => ApiConfig.receiveTimeout;

  // Medical Disclaimer
  static const String medicalDisclaimer =
      'AI Health Assistant provides general health information and does not replace professional medical diagnosis or treatment.';
}
