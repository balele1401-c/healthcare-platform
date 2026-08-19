import '../models/health_profile_model.dart';
import '../models/user_model.dart';

/// Abstract contract for authentication operations.
/// Follows Clean Architecture so MockAuthRepository can later be swapped
/// with LaravelAuthRepository in Phase 6.
abstract class AuthRepository {
  Future<UserModel> login({
    required String email,
    required String password,
  });

  Future<UserModel> register({
    required String name,
    required String email,
    required String phoneNumber,
    required String password,
  });

  Future<void> sendForgotPasswordOtp({
    required String identifier,
  });

  Future<bool> verifyOtp({
    required String identifier,
    required String otpCode,
  });

  Future<void> resetPassword({
    required String identifier,
    required String otpCode,
    required String newPassword,
  });

  Future<UserModel> createHealthProfile({
    required HealthProfileModel profile,
  });

  Future<UserModel?> getCurrentUser();

  Future<bool> isAuthenticated();

  Future<void> logout();
}
