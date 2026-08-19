import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/errors/failures.dart';
import '../../../../core/network/api_client.dart';
import '../../../../core/storage/secure_storage_service.dart';
import '../../data/repositories/api_auth_repository.dart';
import '../../domain/models/health_profile_model.dart';
import '../../domain/models/user_model.dart';
import '../../domain/repositories/auth_repository.dart';

enum AuthStatus {
  initial,
  loading,
  authenticated,
  unauthenticated,
  otpSent,
  error,
}

class AuthState {
  final AuthStatus status;
  final UserModel? user;
  final String? errorMessage;
  final String? otpIdentifier;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.errorMessage,
    this.otpIdentifier,
  });

  bool get isLoading => status == AuthStatus.loading;
  bool get isAuthenticated => status == AuthStatus.authenticated && user != null;

  AuthState copyWith({
    AuthStatus? status,
    UserModel? user,
    String? errorMessage,
    String? otpIdentifier,
  }) {
    return AuthState(
      status: status ?? this.status,
      user: user ?? this.user,
      errorMessage: errorMessage,
      otpIdentifier: otpIdentifier ?? this.otpIdentifier,
    );
  }
}

final secureStorageProvider = Provider<SecureStorageService>((ref) {
  return SecureStorageService();
});

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  final storage = ref.watch(secureStorageProvider);
  return ApiAuthRepository(
    apiClient: apiClient,
    storageService: storage,
  );
});

final authControllerProvider = StateNotifierProvider<AuthController, AuthState>((ref) {
  final repository = ref.watch(authRepositoryProvider);
  return AuthController(repository);
});

class AuthController extends StateNotifier<AuthState> {
  final AuthRepository _repository;

  AuthController(this._repository) : super(const AuthState());

  Future<void> checkAuthStatus() async {
    state = state.copyWith(status: AuthStatus.loading);
    try {
      final user = await _repository.getCurrentUser();
      if (user != null) {
        state = state.copyWith(
          status: AuthStatus.authenticated,
          user: user,
        );
      } else {
        state = state.copyWith(status: AuthStatus.unauthenticated);
      }
    } catch (e) {
      state = state.copyWith(status: AuthStatus.unauthenticated);
    }
  }

  Future<bool> login({
    required String email,
    required String password,
  }) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final user = await _repository.login(email: email, password: password);
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: user,
      );
      return true;
    } on Failure catch (f) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: f.message,
      );
      return false;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: 'An unexpected error occurred. Please try again.',
      );
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String phoneNumber,
    required String password,
  }) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final user = await _repository.register(
        name: name,
        email: email,
        phoneNumber: phoneNumber,
        password: password,
      );
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: user,
      );
      return true;
    } on Failure catch (f) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: f.message,
      );
      return false;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: 'An unexpected error occurred during registration.',
      );
      return false;
    }
  }

  Future<bool> sendForgotPasswordOtp({required String identifier}) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      await _repository.sendForgotPasswordOtp(identifier: identifier);
      state = state.copyWith(
        status: AuthStatus.otpSent,
        otpIdentifier: identifier,
      );
      return true;
    } on Failure catch (f) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: f.message,
      );
      return false;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: 'Unable to send OTP code. Please try again.',
      );
      return false;
    }
  }

  Future<bool> verifyOtp({required String otpCode}) async {
    final identifier = state.otpIdentifier ?? 'user@example.com';
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final isValid = await _repository.verifyOtp(
        identifier: identifier,
        otpCode: otpCode,
      );
      if (isValid) {
        state = state.copyWith(status: AuthStatus.authenticated);
      }
      return isValid;
    } on Failure catch (f) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: f.message,
      );
      return false;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: 'Invalid OTP verification code.',
      );
      return false;
    }
  }

  Future<bool> createHealthProfile({required HealthProfileModel profile}) async {
    state = state.copyWith(status: AuthStatus.loading, errorMessage: null);
    try {
      final updatedUser = await _repository.createHealthProfile(profile: profile);
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: updatedUser,
      );
      return true;
    } on Failure catch (f) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: f.message,
      );
      return false;
    } catch (e) {
      state = state.copyWith(
        status: AuthStatus.error,
        errorMessage: 'Failed to create health profile. Please try again.',
      );
      return false;
    }
  }

  Future<void> logout() async {
    await _repository.logout();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }
}
