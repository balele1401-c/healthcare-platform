import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/auth/data/repositories/mock_auth_repository.dart';
import 'package:healthcare/features/auth/presentation/controllers/auth_controller.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  late MockAuthRepository repository;
  late AuthController controller;

  setUp(() {
    FlutterSecureStorage.setMockInitialValues({});
    repository = MockAuthRepository();
    controller = AuthController(repository);
  });

  group('AuthController Test Suite', () {
    test('initial state is unauthenticated / initial', () {
      expect(controller.state.status, AuthStatus.initial);
      expect(controller.state.isAuthenticated, false);
      expect(controller.state.user, isNull);
    });

    test('login updates state to authenticated on success', () async {
      final success = await controller.login(
        email: 'sarah.jenkins@example.com',
        password: 'Password123!',
      );

      expect(success, true);
      expect(controller.state.status, AuthStatus.authenticated);
      expect(controller.state.user?.name, 'Sarah Jenkins');
      expect(controller.state.isAuthenticated, true);
    });

    test('login sets error state on invalid password', () async {
      final success = await controller.login(
        email: 'sarah.jenkins@example.com',
        password: '123',
      );

      expect(success, false);
      expect(controller.state.status, AuthStatus.error);
      expect(controller.state.errorMessage, isNotNull);
    });

    test('signInWithGoogle updates state to authenticated on success', () async {
      final success = await controller.signInWithGoogle();

      expect(success, true);
      expect(controller.state.status, AuthStatus.authenticated);
      expect(controller.state.user?.name, 'Sarah Jenkins (Google)');
      expect(controller.state.isAuthenticated, true);
    });

    test('logout resets state to unauthenticated', () async {
      await controller.login(
        email: 'sarah.jenkins@example.com',
        password: 'Password123!',
      );
      expect(controller.state.isAuthenticated, true);

      await controller.logout();
      expect(controller.state.status, AuthStatus.unauthenticated);
      expect(controller.state.user, isNull);
    });
  });
}
