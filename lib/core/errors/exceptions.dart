/// Data-layer exceptions.
class AppException implements Exception {
  final String message;
  final String? code;

  const AppException(this.message, [this.code]);

  @override
  String toString() => 'AppException: $message (code: $code)';
}

class ServerException extends AppException {
  const ServerException([super.message = 'Internal server exception', super.code]);
}

class AuthException extends AppException {
  const AuthException([super.message = 'Authentication exception', super.code]);
}

class ValidationException extends AppException {
  const ValidationException([super.message = 'Validation exception', super.code]);
}

class NetworkException extends AppException {
  const NetworkException([super.message = 'Network connection exception', super.code]);
}

class CacheException extends AppException {
  const CacheException([super.message = 'Cache exception', super.code]);
}
