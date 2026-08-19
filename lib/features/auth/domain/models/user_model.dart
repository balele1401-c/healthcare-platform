/// Domain model representing an authenticated user.
class UserModel {
  final String id;
  final String name;
  final String email;
  final String role;
  final String? phoneNumber;
  final String? avatarUrl;
  final bool isHealthProfileCompleted;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.phoneNumber,
    this.avatarUrl,
    this.isHealthProfileCompleted = false,
  });

  UserModel copyWith({
    String? id,
    String? name,
    String? email,
    String? role,
    String? phoneNumber,
    String? avatarUrl,
    bool? isHealthProfileCompleted,
  }) {
    return UserModel(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      role: role ?? this.role,
      phoneNumber: phoneNumber ?? this.phoneNumber,
      avatarUrl: avatarUrl ?? this.avatarUrl,
      isHealthProfileCompleted: isHealthProfileCompleted ?? this.isHealthProfileCompleted,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'phone': phoneNumber,
      'phone_number': phoneNumber,
      'avatar_url': avatarUrl,
      'is_health_profile_completed': isHealthProfileCompleted,
    };
  }

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final patientData = json['patient'];
    final bool hasProfile = (patientData is Map<String, dynamic> && patientData.isNotEmpty) ||
        (json['is_health_profile_completed'] == true);

    return UserModel(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role']?.toString() ?? 'patient',
      phoneNumber: json['phone'] ?? json['phone_number'],
      avatarUrl: json['avatar_url'] ?? (patientData is Map ? patientData['profile_photo'] : null),
      isHealthProfileCompleted: hasProfile,
    );
  }
}
