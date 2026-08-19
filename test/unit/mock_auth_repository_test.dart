import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/errors/failures.dart';
import 'package:healthcare/features/auth/data/repositories/mock_auth_repository.dart';
import 'package:healthcare/features/auth/domain/models/health_profile_model.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  late MockAuthRepository repository;

  setUp(() {
    FlutterSecureStorage.setMockInitialValues({});
    repository = MockAuthRepository();
  });

  group('MockAuthRepository Test Suite', () {
    test('login succeeds with valid credentials', () async {
      final user = await repository.login(
        email: 'sarah.jenkins@example.com',
        password: 'Password123!',
      );

      expect(user.id, 'patient_101');
      expect(user.name, 'Sarah Jenkins');
      expect(user.email, 'sarah.jenkins@example.com');
      expect(user.role, 'patient');
    });

    test('login throws AuthFailure for short password', () async {
      expect(
        () => repository.login(
          email: 'sarah.jenkins@example.com',
          password: '123',
        ),
        throwsA(isA<AuthFailure>()),
      );
    });

    test('register creates new patient user', () async {
      final user = await repository.register(
        name: 'Jane Smith',
        email: 'jane.smith@example.com',
        phoneNumber: '+1 555-019-4444',
        password: 'SecurePassword123!',
      );

      expect(user.name, 'Jane Smith');
      expect(user.email, 'jane.smith@example.com');
      expect(user.isHealthProfileCompleted, false);
    });

    test('verifyOtp succeeds with 123456', () async {
      await repository.sendForgotPasswordOtp(identifier: 'sarah@example.com');
      final result = await repository.verifyOtp(
        identifier: 'sarah@example.com',
        otpCode: '123456',
      );
      expect(result, true);
    });

    test('verifyOtp throws ValidationFailure for invalid code', () async {
      expect(
        () => repository.verifyOtp(
          identifier: 'sarah@example.com',
          otpCode: '999999',
        ),
        throwsA(isA<ValidationFailure>()),
      );
    });

    test('createHealthProfile marks profile completed', () async {
      final profile = HealthProfileModel(
        dateOfBirth: DateTime(1990, 1, 1),
        gender: 'female',
        bloodType: 'A+',
        emergencyContactName: 'John Jenkins',
        emergencyContactPhone: '+1 555-019-1111',
      );

      final user = await repository.createHealthProfile(profile: profile);
      expect(user.isHealthProfileCompleted, true);
    });
  });
}
