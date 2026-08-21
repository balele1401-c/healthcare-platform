/// Centralized API configuration for HealthCare Mobile & Web clients.
/// Directs all production traffic to the live Laravel 12 API deployed on Railway.
class ApiConfig {
  /// Production Laravel API Railway URL
  static const String productionBaseUrl =
      'https://healthcare-platform-production-5197.up.railway.app/api/v1';

  /// Local development fallback URL
  static const String localWebBaseUrl = 'http://127.0.0.1:8000/api/v1';
  static const String localAndroidBaseUrl = 'http://10.0.2.2:8000/api/v1';

  /// Custom runtime override (if configured via settings or CLI)
  static String? _customBaseUrl;

  static set customBaseUrl(String? url) {
    _customBaseUrl = url;
  }

  /// Primary Base URL for all API requests.
  /// Defaults to production Railway API unless overridden via `--dart-define=API_BASE_URL=...`
  /// or explicit runtime configuration.
  static String get baseUrl {
    if (_customBaseUrl != null && _customBaseUrl!.isNotEmpty) {
      return _customBaseUrl!;
    }

    const envUrl = String.fromEnvironment('API_BASE_URL');
    if (envUrl.isNotEmpty) {
      return envUrl;
    }

    // Default to Live Railway Production Backend
    return productionBaseUrl;
  }

  /// Base domain URL without API prefix
  static const String serverHost = 'https://healthcare-platform-production-5197.up.railway.app';

  /// Network Timeouts
  static const Duration connectTimeout = Duration(seconds: 20);
  static const Duration receiveTimeout = Duration(seconds: 20);
}
