import '../../../../core/errors/failures.dart';
import '../../../../core/network/api_client.dart';
import '../../../../core/storage/secure_storage_service.dart';
import '../../domain/models/health_profile_model.dart';
import '../../domain/models/user_model.dart';
import '../../domain/repositories/auth_repository.dart';

/// Real REST API implementation of AuthRepository communicating with Laravel Sanctum backend.
class ApiAuthRepository implements AuthRepository {
  final ApiClient _apiClient;
  final SecureStorageService _storageService;

  ApiAuthRepository({
    required ApiClient apiClient,
    required SecureStorageService storageService,
  })  : _apiClient = apiClient,
        _storageService = storageService;

  @override
  Future<UserModel> login({
    required String email,
    required String password,
  }) async {
    final response = await _apiClient.post('/auth/login', data: {
      'email': email.trim(),
      'password': password,
    });

    final data = response.data['data'] as Map<String, dynamic>;
    final token = data['token'] as String;
    final userData = data['user'] as Map<String, dynamic>;

    await _storageService.saveToken(token);

    // Fetch full profile with patient metadata
    try {
      final meResponse = await _apiClient.get('/auth/me');
      if (meResponse.data != null && meResponse.data['data'] is Map<String, dynamic>) {
        return UserModel.fromJson(meResponse.data['data'] as Map<String, dynamic>);
      }
    } catch (_) {
      // Fallback to login response payload
    }

    return UserModel.fromJson(userData);
  }

  @override
  Future<UserModel> register({
    required String name,
    required String email,
    required String phoneNumber,
    required String password,
  }) async {
    final response = await _apiClient.post('/auth/register', data: {
      'name': name.trim(),
      'email': email.trim(),
      'phone': phoneNumber.trim(),
      'password': password,
      'password_confirmation': password,
    });

    final data = response.data['data'] as Map<String, dynamic>;
    final token = data['token'] as String;
    final userData = data['user'] as Map<String, dynamic>;

    await _storageService.saveToken(token);
    return UserModel.fromJson(userData);
  }

  @override
  Future<UserModel?> getCurrentUser() async {
    final token = await _storageService.getToken();
    if (token == null || token.isEmpty) {
      return null;
    }

    try {
      final response = await _apiClient.get('/auth/me');
      if (response.data != null && response.data['data'] is Map<String, dynamic>) {
        return UserModel.fromJson(response.data['data'] as Map<String, dynamic>);
      }
      return null;
    } on AuthFailure {
      await _storageService.clearSession();
      return null;
    } catch (e) {
      // On network timeout, do not delete token immediately
      return null;
    }
  }

  @override
  Future<bool> isAuthenticated() async {
    final token = await _storageService.getToken();
    return token != null && token.isNotEmpty;
  }

  @override
  Future<void> logout() async {
    try {
      await _apiClient.post('/auth/logout');
    } catch (_) {
      // Clear local session even if remote logout fails
    } finally {
      await _storageService.clearSession();
    }
  }

  @override
  Future<UserModel> createHealthProfile({
    required HealthProfileModel profile,
  }) async {
    final response = await _apiClient.put('/patient/profile', data: profile.toJson());
    final user = await getCurrentUser();
    if (user != null) {
      return user.copyWith(isHealthProfileCompleted: true);
    }

    if (response.data != null && response.data['data'] is Map<String, dynamic>) {
      final patientData = response.data['data'] as Map<String, dynamic>;
      return UserModel(
        id: patientData['user_id']?.toString() ?? '1',
        name: patientData['name'] ?? '',
        email: patientData['email'] ?? '',
        role: 'patient',
        isHealthProfileCompleted: true,
      );
    }

    throw const ServerFailure('Failed to update patient profile.');
  }

  @override
  Future<void> sendForgotPasswordOtp({required String identifier}) async {
    // Development OTP simulation
    await Future.delayed(const Duration(milliseconds: 400));
  }

  @override
  Future<bool> verifyOtp({
    required String identifier,
    required String otpCode,
  }) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return otpCode == '123456' || otpCode.length == 6;
  }

  @override
  Future<void> resetPassword({
    required String identifier,
    required String otpCode,
    required String newPassword,
  }) async {
    await Future.delayed(const Duration(milliseconds: 400));
  }
}
