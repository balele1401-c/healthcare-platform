import 'package:flutter/foundation.dart';

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

  // Custom override if provided at runtime or compilation define
  static String? _customBaseApiUrl;

  static set customBaseApiUrl(String? url) {
    _customBaseApiUrl = url;
  }

  /// Automatically resolves the appropriate base API URL based on client environment:
  /// - Flutter Web (Chrome / Edge / Firefox): http://127.0.0.1:8000/api/v1
  /// - Desktop (Windows / macOS / Linux) / iOS Simulator: http://127.0.0.1:8000/api/v1
  /// - Android Emulator: http://10.0.2.2:8000/api/v1
  /// - Physical device / custom: Configured via --dart-define=API_BASE_URL=... or customBaseApiUrl
  static String get baseApiUrl {
    if (_customBaseApiUrl != null && _customBaseApiUrl!.isNotEmpty) {
      return _customBaseApiUrl!;
    }

    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }

    if (kIsWeb) {
      return 'http://127.0.0.1:8000/api/v1';
    }

    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return 'http://10.0.2.2:8000/api/v1';
      case TargetPlatform.iOS:
      case TargetPlatform.macOS:
      case TargetPlatform.windows:
      case TargetPlatform.linux:
      case TargetPlatform.fuchsia:
        return 'http://127.0.0.1:8000/api/v1';
    }
  }

  static const Duration connectTimeout = Duration(seconds: 15);
  static const Duration receiveTimeout = Duration(seconds: 15);

  // Medical Disclaimer
  static const String medicalDisclaimer =
      'AI Health Assistant provides general health information and does not replace professional medical diagnosis or treatment.';
}
