import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';

class PatientProfileState {
  final String id;
  final String fullName;
  final String email;
  final String phoneNumber;
  final DateTime dateOfBirth;
  final String gender;
  final String bloodType;
  final String heightCm;
  final String weightKg;
  final String address;
  final String emergencyContactName;
  final String emergencyContactPhone;
  final String avatarUrl;
  final bool pushNotificationsEnabled;
  final bool biometricAuthEnabled;
  final String language;
  final bool isLoading;

  const PatientProfileState({
    this.id = 'patient_101',
    this.fullName = 'Sarah Jenkins',
    this.email = 'sarah.jenkins@example.com',
    this.phoneNumber = '+1 (555) 019-2834',
    required this.dateOfBirth,
    this.gender = 'Female',
    this.bloodType = 'A+',
    this.heightCm = '168',
    this.weightKg = '64.5',
    this.address = '742 Evergreen Terrace, Apt 4B, Springfield, OR',
    this.emergencyContactName = 'David Jenkins (Spouse)',
    this.emergencyContactPhone = '+1 (555) 019-5821',
    this.avatarUrl = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
    this.pushNotificationsEnabled = true,
    this.biometricAuthEnabled = true,
    this.language = 'English (US)',
    this.isLoading = false,
  });

  PatientProfileState copyWith({
    String? id,
    String? fullName,
    String? email,
    String? phoneNumber,
    DateTime? dateOfBirth,
    String? gender,
    String? bloodType,
    String? heightCm,
    String? weightKg,
    String? address,
    String? emergencyContactName,
    String? emergencyContactPhone,
    String? avatarUrl,
    bool? pushNotificationsEnabled,
    bool? biometricAuthEnabled,
    String? language,
    bool? isLoading,
  }) {
    return PatientProfileState(
      id: id ?? this.id,
      fullName: fullName ?? this.fullName,
      email: email ?? this.email,
      phoneNumber: phoneNumber ?? this.phoneNumber,
      dateOfBirth: dateOfBirth ?? this.dateOfBirth,
      gender: gender ?? this.gender,
      bloodType: bloodType ?? this.bloodType,
      heightCm: heightCm ?? this.heightCm,
      weightKg: weightKg ?? this.weightKg,
      address: address ?? this.address,
      emergencyContactName: emergencyContactName ?? this.emergencyContactName,
      emergencyContactPhone: emergencyContactPhone ?? this.emergencyContactPhone,
      avatarUrl: avatarUrl ?? this.avatarUrl,
      pushNotificationsEnabled: pushNotificationsEnabled ?? this.pushNotificationsEnabled,
      biometricAuthEnabled: biometricAuthEnabled ?? this.biometricAuthEnabled,
      language: language ?? this.language,
      isLoading: isLoading ?? this.isLoading,
    );
  }
}

class PatientProfileNotifier extends StateNotifier<PatientProfileState> {
  final ApiClient _apiClient;

  PatientProfileNotifier(this._apiClient)
      : super(
          PatientProfileState(
            dateOfBirth: DateTime(1992, 6, 14),
          ),
        ) {
    loadProfile();
  }

  Future<void> loadProfile() async {
    try {
      final response = await _apiClient.get('/patient/profile');
      final data = response.data['data'] as Map<String, dynamic>?;
      if (data != null) {
        DateTime dob = state.dateOfBirth;
        if (data['date_of_birth'] != null) {
          try {
            dob = DateTime.parse(data['date_of_birth'].toString());
          } catch (_) {}
        }

        state = state.copyWith(
          id: data['id']?.toString() ?? state.id,
          fullName: data['name'] ?? state.fullName,
          email: data['email'] ?? state.email,
          phoneNumber: data['phone'] ?? state.phoneNumber,
          dateOfBirth: dob,
          gender: data['gender'] != null ? data['gender'].toString().toUpperCase() : state.gender,
          bloodType: data['blood_type'] ?? state.bloodType,
          heightCm: data['height_cm']?.toString() ?? state.heightCm,
          weightKg: data['weight_kg']?.toString() ?? state.weightKg,
          address: data['address'] ?? state.address,
          emergencyContactName: data['emergency_contact_name'] ?? state.emergencyContactName,
          emergencyContactPhone: data['emergency_contact_phone'] ?? state.emergencyContactPhone,
          avatarUrl: data['profile_photo'] ?? state.avatarUrl,
        );
      }
    } catch (_) {
      // Retain standard profile state
    }
  }

  Future<bool> updateProfile({
    String? fullName,
    String? phoneNumber,
    DateTime? dateOfBirth,
    String? gender,
    String? bloodType,
    String? heightCm,
    String? weightKg,
    String? address,
    String? emergencyContactName,
    String? emergencyContactPhone,
  }) async {
    state = state.copyWith(
      fullName: fullName,
      phoneNumber: phoneNumber,
      dateOfBirth: dateOfBirth,
      gender: gender,
      bloodType: bloodType,
      heightCm: heightCm,
      weightKg: weightKg,
      address: address,
      emergencyContactName: emergencyContactName,
      emergencyContactPhone: emergencyContactPhone,
      isLoading: true,
    );

    try {
      await _apiClient.put('/patient/profile', data: {
        'name': fullName ?? state.fullName,
        'phone': phoneNumber ?? state.phoneNumber,
        'date_of_birth': (dateOfBirth ?? state.dateOfBirth).toIso8601String().split('T').first,
        'gender': (gender ?? state.gender).toLowerCase(),
        'blood_type': bloodType ?? state.bloodType,
        'height_cm': double.tryParse(heightCm ?? state.heightCm),
        'weight_kg': double.tryParse(weightKg ?? state.weightKg),
        'address': address ?? state.address,
        'emergency_contact_name': emergencyContactName ?? state.emergencyContactName,
        'emergency_contact_phone': emergencyContactPhone ?? state.emergencyContactPhone,
      });
      state = state.copyWith(isLoading: false);
      return true;
    } catch (_) {
      state = state.copyWith(isLoading: false);
      return false;
    }
  }

  void togglePushNotifications(bool val) {
    state = state.copyWith(pushNotificationsEnabled: val);
  }

  void toggleBiometrics(bool val) {
    state = state.copyWith(biometricAuthEnabled: val);
  }

  void setLanguage(String lang) {
    state = state.copyWith(language: lang);
  }
}

final patientProfileProvider = StateNotifierProvider<PatientProfileNotifier, PatientProfileState>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return PatientProfileNotifier(apiClient);
});
