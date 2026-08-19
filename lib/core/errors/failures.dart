/// Domain-level failure representation.
abstract class Failure {
  final String message;
  final String? code;

  const Failure(this.message, [this.code]);

  @override
  String toString() => message;
}

class ServerFailure extends Failure {
  const ServerFailure([super.message = 'A server error occurred. Please try again later.', super.code]);
}

class AuthFailure extends Failure {
  const AuthFailure([super.message = 'Authentication failed. Please check your credentials.', super.code]);
}

class ValidationFailure extends Failure {
  const ValidationFailure([super.message = 'Invalid input provided. Please check the form.', super.code]);
}

class NetworkFailure extends Failure {
  const NetworkFailure([super.message = 'No internet connection. Please verify your network.', super.code]);
}

class CacheFailure extends Failure {
  const CacheFailure([super.message = 'Unable to access local storage.', super.code]);
}
