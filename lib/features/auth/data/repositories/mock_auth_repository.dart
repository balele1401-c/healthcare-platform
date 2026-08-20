import 'package:healthcare/core/errors/failures.dart';
import 'package:healthcare/core/storage/secure_storage_service.dart';
import '../../domain/models/health_profile_model.dart';
import '../../domain/models/user_model.dart';
import '../../domain/repositories/auth_repository.dart';

/// In-memory mock authentication repository for unit and widget testing.
class MockAuthRepository implements AuthRepository {
  final SecureStorageService _storageService;
  bool isNewGoogleUser;

  UserModel? _currentUser;
  String? _lastSentOtp;

  MockAuthRepository({
    SecureStorageService? storageService,
    this.isNewGoogleUser = false,
  }) : _storageService = storageService ?? SecureStorageService();

  @override
  Future<UserModel> login({
    required String email,
    required String password,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));

    final normalizedEmail = email.trim().toLowerCase();

    // Simulated credential check
    if (password.length < 6) {
      throw const AuthFailure('Invalid password. Must be at least 6 characters.');
    }

    if (normalizedEmail.contains('error')) {
      throw const AuthFailure('Invalid email or password. Please try again.');
    }

    // Default mock patient profile matching Stitch "Good morning, Sarah"
    _currentUser = UserModel(
      id: 'patient_101',
      name: normalizedEmail.startsWith('sarah') ? 'Sarah Jenkins' : 'John Doe',
      email: normalizedEmail,
      role: 'patient',
      phoneNumber: '+1 555-019-2834',
      avatarUrl: null,
      isHealthProfileCompleted: true,
    );

    await _storageService.saveToken('mock_sanctum_token_${_currentUser!.id}');
    return _currentUser!;
  }

  @override
  Future<UserModel> register({
    required String name,
    required String email,
    required String phoneNumber,
    required String password,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));

    final normalizedEmail = email.trim().toLowerCase();

    if (normalizedEmail.contains('taken')) {
      throw const ValidationFailure('This email address is already registered.');
    }

    _currentUser = UserModel(
      id: 'patient_${DateTime.now().millisecondsSinceEpoch}',
      name: name.trim(),
      email: normalizedEmail,
      role: 'patient',
      phoneNumber: phoneNumber.trim(),
      avatarUrl: null,
      isHealthProfileCompleted: false, // New users need health profile setup
    );

    await _storageService.saveToken('mock_sanctum_token_${_currentUser!.id}');
    return _currentUser!;
  }

  @override
  Future<UserModel?> signInWithGoogle() async {
    await Future.delayed(const Duration(milliseconds: 200));
    _currentUser = UserModel(
      id: 'patient_google_101',
      name: 'Sarah Jenkins (Google)',
      email: 'sarah.jenkins@gmail.com',
      role: 'patient',
      phoneNumber: '+1 555-019-2834',
      avatarUrl: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
      isHealthProfileCompleted: !isNewGoogleUser,
    );

    await _storageService.saveToken('mock_google_token_${_currentUser!.id}');
    return _currentUser;
  }

  @override
  Future<void> sendForgotPasswordOtp({
    required String identifier,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));

    if (identifier.contains('notfound')) {
      throw const AuthFailure('No account found with this email or phone.');
    }

    _lastSentOtp = '123456'; // Default deterministic mock OTP
  }

  @override
  Future<bool> verifyOtp({
    required String identifier,
    required String otpCode,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));

    if (otpCode == '123456' || otpCode == _lastSentOtp || otpCode == '000000') {
      return true;
    }

    throw const ValidationFailure('Invalid or expired OTP code.');
  }

  @override
  Future<void> resetPassword({
    required String identifier,
    required String otpCode,
    required String newPassword,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));
    await verifyOtp(identifier: identifier, otpCode: otpCode);
  }

  @override
  Future<UserModel> createHealthProfile({
    required HealthProfileModel profile,
  }) async {
    await Future.delayed(const Duration(milliseconds: 200));

    if (_currentUser == null) {
      _currentUser = const UserModel(
        id: 'patient_101',
        name: 'Sarah Jenkins',
        email: 'sarah.jenkins@example.com',
        role: 'patient',
        isHealthProfileCompleted: true,
      );
    } else {
      _currentUser = _currentUser!.copyWith(isHealthProfileCompleted: true);
    }

    return _currentUser!;
  }

  @override
  Future<UserModel?> getCurrentUser() async {
    if (_currentUser != null) return _currentUser;

    final token = await _storageService.getToken();
    if (token != null && token.isNotEmpty) {
      _currentUser = const UserModel(
        id: 'patient_101',
        name: 'Sarah Jenkins',
        email: 'sarah.jenkins@example.com',
        role: 'patient',
        isHealthProfileCompleted: true,
      );
      return _currentUser;
    }

    return null;
  }

  @override
  Future<bool> isAuthenticated() async {
    final token = await _storageService.getToken();
    return token != null && token.isNotEmpty;
  }

  @override
  Future<void> logout() async {
    await _storageService.clearSession();
    _currentUser = null;
  }
}
