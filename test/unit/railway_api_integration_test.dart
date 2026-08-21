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

    test('Multi-Role Login & Authorization Matrix (Patient, Doctor, Staff, Admin, Owner)', () async {
      final roles = [
        {'email': 'patient@healthcare.test', 'role': 'patient'},
        {'email': 'doctor@healthcare.test', 'role': 'doctor'},
        {'email': 'staff@healthcare.test', 'role': 'staff'},
        {'email': 'admin@healthcare.test', 'role': 'admin'},
        {'email': 'owner@healthcare.test', 'role': 'owner'},
      ];

      for (final r in roles) {
        // 1. Authenticate with single login endpoint
        final loginRes = await dio.post('/auth/login', data: {
          'email': r['email'],
          'password': 'Password123!',
        });

        expect(loginRes.statusCode, 200);
        expect(loginRes.data['success'], isTrue);
        expect(loginRes.data['data']['user']['role'], r['role']);
        final token = loginRes.data['data']['token'] as String;
        expect(token, isNotEmpty);

        // 2. Fetch authenticated profile
        final meRes = await dio.get(
          '/auth/me',
          options: Options(headers: {'Authorization': 'Bearer $token'}),
        );
        expect(meRes.statusCode, 200);
        expect(meRes.data['data']['role'], r['role']);
      }
    });

    test('Role-Based Authorization Enforcement Tests', () async {
      // Login as Patient
      final patientLogin = await dio.post('/auth/login', data: {
        'email': 'patient@healthcare.test',
        'password': 'Password123!',
      });
      final patientToken = patientLogin.data['data']['token'];

      // Patient -> Patient Endpoint = Allowed
      final patientSelfRes = await dio.get(
        '/patient/profile',
        options: Options(headers: {'Authorization': 'Bearer $patientToken'}),
      );
      expect(patientSelfRes.statusCode, 200);

      // Patient -> Admin Dashboard = Forbidden (403)
      try {
        await dio.get(
          '/admin/dashboard',
          options: Options(headers: {'Authorization': 'Bearer $patientToken'}),
        );
        fail('Patient should be forbidden from admin dashboard');
      } on DioException catch (e) {
        expect(e.response?.statusCode, 403);
      }

      // Login as Doctor
      final docLogin = await dio.post('/auth/login', data: {
        'email': 'doctor@healthcare.test',
        'password': 'Password123!',
      });
      final docToken = docLogin.data['data']['token'];

      // Doctor -> Doctor Dashboard = Allowed
      final docDashRes = await dio.get(
        '/doctor/dashboard',
        options: Options(headers: {'Authorization': 'Bearer $docToken'}),
      );
      expect(docDashRes.statusCode, 200);

      // Doctor -> Admin Dashboard = Forbidden (403)
      try {
        await dio.get(
          '/admin/dashboard',
          options: Options(headers: {'Authorization': 'Bearer $docToken'}),
        );
        fail('Doctor should be forbidden from admin dashboard');
      } on DioException catch (e) {
        expect(e.response?.statusCode, 403);
      }

      // Login as Staff
      final staffLogin = await dio.post('/auth/login', data: {
        'email': 'staff@healthcare.test',
        'password': 'Password123!',
      });
      final staffToken = staffLogin.data['data']['token'];

      // Staff -> Staff Dashboard = Allowed
      final staffDashRes = await dio.get(
        '/staff/dashboard',
        options: Options(headers: {'Authorization': 'Bearer $staffToken'}),
      );
      expect(staffDashRes.statusCode, 200);

      // Staff -> Owner Dashboard = Forbidden (403)
      try {
        await dio.get(
          '/owner/dashboard',
          options: Options(headers: {'Authorization': 'Bearer $staffToken'}),
        );
        fail('Staff should be forbidden from owner dashboard');
      } on DioException catch (e) {
        expect(e.response?.statusCode, 403);
      }

      // Login as Admin
      final adminLogin = await dio.post('/auth/login', data: {
        'email': 'admin@healthcare.test',
        'password': 'Password123!',
      });
      final adminToken = adminLogin.data['data']['token'];

      // Admin -> Admin Dashboard = Allowed
      final adminDashRes = await dio.get(
        '/admin/dashboard',
        options: Options(headers: {'Authorization': 'Bearer $adminToken'}),
      );
      expect(adminDashRes.statusCode, 200);

      // Login as Owner
      final ownerLogin = await dio.post('/auth/login', data: {
        'email': 'owner@healthcare.test',
        'password': 'Password123!',
      });
      final ownerToken = ownerLogin.data['data']['token'];

      // Owner -> Owner Dashboard = Allowed
      final ownerDashRes = await dio.get(
        '/owner/dashboard',
        options: Options(headers: {'Authorization': 'Bearer $ownerToken'}),
      );
      expect(ownerDashRes.statusCode, 200);
    });
  });
}
