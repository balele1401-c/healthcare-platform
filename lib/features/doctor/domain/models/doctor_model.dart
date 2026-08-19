class SpecialtyModel {
  final String id;
  final String name;
  final String iconName;
  final int doctorCount;

  const SpecialtyModel({
    required this.id,
    required this.name,
    required this.iconName,
    required this.doctorCount,
  });

  factory SpecialtyModel.fromJson(Map<String, dynamic> json) {
    return SpecialtyModel(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? '',
      iconName: json['icon'] ?? 'medical_services',
      doctorCount: json['doctors_count'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'icon': iconName,
      'doctors_count': doctorCount,
    };
  }
}

class DoctorModel {
  final String id;
  final String name;
  final String specialty;
  final String specialtyId;
  final String title;
  final double rating;
  final int reviewCount;
  final int experienceYears;
  final int patientCount;
  final double consultationFee;
  final String avatarUrl;
  final String biography;
  final String education;
  final String clinicName;
  final String clinicAddress;
  final bool isAvailableToday;
  final List<String> availableDays;
  final List<String> availableTimeSlots;

  const DoctorModel({
    required this.id,
    required this.name,
    required this.specialty,
    required this.specialtyId,
    required this.title,
    required this.rating,
    required this.reviewCount,
    required this.experienceYears,
    required this.patientCount,
    required this.consultationFee,
    required this.avatarUrl,
    required this.biography,
    required this.education,
    required this.clinicName,
    required this.clinicAddress,
    this.isAvailableToday = true,
    this.availableDays = const ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
    this.availableTimeSlots = const [
      '09:00 AM',
      '09:30 AM',
      '10:00 AM',
      '10:30 AM',
      '01:00 PM',
      '01:30 PM',
      '02:00 PM',
      '03:30 PM',
      '04:00 PM',
    ],
  });

  factory DoctorModel.fromJson(Map<String, dynamic> json) {
    final specialtyData = json['specialty'];
    final String specName = json['specialty_name'] ??
        (specialtyData is Map ? specialtyData['name'] : '') ??
        'Specialist';
    final String specId = (specialtyData is Map ? specialtyData['id']?.toString() : null) ??
        json['specialty_id']?.toString() ??
        '';

    final schedulesList = json['schedules'];
    List<String> days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    List<String> timeSlots = [
      '09:00 AM',
      '09:30 AM',
      '10:00 AM',
      '10:30 AM',
      '01:00 PM',
      '01:30 PM',
      '02:00 PM',
    ];

    if (schedulesList is List && schedulesList.isNotEmpty) {
      final dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      final mappedDays = schedulesList
          .map((s) => s is Map && s.containsKey('day_of_week') ? dayNames[s['day_of_week'] as int] : 'Mon')
          .toSet()
          .toList();
      if (mappedDays.isNotEmpty) {
        days = mappedDays;
      }
    }

    return DoctorModel(
      id: json['id']?.toString() ?? '',
      name: json['name'] ?? 'Doctor',
      specialty: specName,
      specialtyId: specId,
      title: 'MD, FACC',
      rating: (json['rating'] as num?)?.toDouble() ?? 4.9,
      reviewCount: (json['review_count'] as num?)?.toInt() ?? 120,
      experienceYears: (json['experience_years'] as num?)?.toInt() ?? 8,
      patientCount: 500,
      consultationFee: (json['consultation_fee'] as num?)?.toDouble() ?? 75.0,
      avatarUrl: json['avatar_url'] ??
          json['profile_photo'] ??
          'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      biography: json['biography'] ?? 'Board-certified medical practitioner.',
      education: json['education'] ?? 'Medical University, MD',
      clinicName: json['facility'] ?? 'Metropolitan Medical Center',
      clinicAddress: '742 Evergreen Terrace, Suite 402',
      isAvailableToday: json['status'] == 'active',
      availableDays: days,
      availableTimeSlots: timeSlots,
    );
  }
}
