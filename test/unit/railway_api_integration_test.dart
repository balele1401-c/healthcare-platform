import 'package:dio/dio.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/core/config/api_config.dart';

void main() {
  group('Railway Laravel Production API Live Integration Tests', () {
    late Dio dio;

    setUp(() {
      dio = Dio(
        BaseOptions(
          baseUrl: ApiConfig.productionBaseUrl,
          connectTimeout: const Duration(seconds: 20),
          receiveTimeout: const Duration(seconds: 20),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        ),
      );
    });

    test('GET /health returns healthy production status', () async {
      final response = await dio.get('/health');
      expect(response.statusCode, 200);
      expect(response.data['success'], isTrue);
      expect(response.data['data']['status'], 'healthy');
      expect(response.data['data']['database'], 'connected');
    });

    test('GET /specialties returns specialties list', () async {
      final response = await dio.get('/specialties');
      expect(response.statusCode, 200);
      expect(response.data['success'], isTrue);
      expect(response.data['data'], isA<List>());
    });

    test('GET /doctors returns doctors pagination payload', () async {
      final response = await dio.get('/doctors');
      expect(response.statusCode, 200);
      expect(response.data['success'], isTrue);
      expect(response.data['data'], isA<List>());
      expect(response.data['meta'], isNotNull);
    });

    test('POST /auth/login with invalid credentials returns 401/422 validation error', () async {
      try {
        await dio.post('/auth/login', data: {
          'email': 'nonexistent.user.test@example.com',
          'password': 'WrongPassword123!',
        });
        fail('Should have thrown DioException');
      } on DioException catch (e) {
        expect(e.response?.statusCode, anyOf(401, 422));
        expect(e.response?.data['success'], isFalse);
      }
    });

    test('End-to-end Patient Register, Login, and Profile on Railway API', () async {
      final timestamp = DateTime.now().millisecondsSinceEpoch;
      final testEmail = 'patient.test.$timestamp@example.com';
      final testPassword = 'SecurePassword123!';

      // 1. Register
      final regResponse = await dio.post('/auth/register', data: {
        'name': 'Test Integration Patient',
        'email': testEmail,
        'phone': '+1555019${timestamp.toString().substring(timestamp.toString().length - 4)}',
        'password': testPassword,
        'password_confirmation': testPassword,
      });

      expect(regResponse.statusCode, anyOf(200, 201));
      expect(regResponse.data['success'], isTrue);
      final token = regResponse.data['data']['token'] as String;
      expect(token, isNotEmpty);

      // 2. Authenticated Profile Fetch
      final profileResponse = await dio.get(
        '/patient/profile',
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      expect(profileResponse.statusCode, 200);
      expect(profileResponse.data['success'], isTrue);
      expect(profileResponse.data['data']['email'], testEmail);

      // 3. Authenticated Appointments List
      final aptResponse = await dio.get(
        '/patient/appointments',
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      expect(aptResponse.statusCode, 200);
      expect(aptResponse.data['success'], isTrue);
      expect(aptResponse.data['data'], isA<List>());

      // 4. Logout
      final logoutResponse = await dio.post(
        '/auth/logout',
        options: Options(headers: {'Authorization': 'Bearer $token'}),
      );

      expect(logoutResponse.statusCode, 200);
      expect(logoutResponse.data['success'], isTrue);
    });
  });
}
