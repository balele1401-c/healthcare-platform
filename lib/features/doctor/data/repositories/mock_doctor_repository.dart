import '../../domain/models/doctor_model.dart';
import '../../domain/repositories/doctor_repository.dart';

class MockDoctorRepository implements DoctorRepository {
  static const List<SpecialtyModel> specialties = [
    SpecialtyModel(id: 'all', name: 'All Doctors', iconName: 'medical_services', doctorCount: 24),
    SpecialtyModel(id: 'cardiology', name: 'Cardiology', iconName: 'favorite', doctorCount: 6),
    SpecialtyModel(id: 'dermatology', name: 'Dermatology', iconName: 'face', doctorCount: 4),
    SpecialtyModel(id: 'neurology', name: 'Neurology', iconName: 'psychology', doctorCount: 3),
    SpecialtyModel(id: 'pediatrics', name: 'Pediatrics', iconName: 'child_care', doctorCount: 5),
    SpecialtyModel(id: 'orthopedics', name: 'Orthopedics', iconName: 'accessible', doctorCount: 4),
    SpecialtyModel(id: 'general', name: 'General Medicine', iconName: 'local_hospital', doctorCount: 8),
  ];

  static final List<DoctorModel> _mockDoctors = [
    const DoctorModel(
      id: 'doc_1',
      name: 'Dr. Emily Chen',
      specialty: 'Senior Cardiologist',
      specialtyId: 'cardiology',
      title: 'MD, FACC - Cardiovascular Specialist',
      rating: 4.9,
      reviewCount: 128,
      experienceYears: 12,
      patientCount: 2450,
      consultationFee: 75.0,
      avatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. Emily Chen is a board-certified Senior Cardiologist with over 12 years of experience specializing in preventive cardiology, echocardiography, and non-invasive cardiovascular interventions. She is dedicated to evidence-based, patient-centered care.',
      education: 'Harvard Medical School (MD) • Johns Hopkins Hospital (Residency)',
      clinicName: 'Metropolitan Heart & Vascular Institute',
      clinicAddress: '742 Evergreen Terrace, Medical District, Suite 400',
      isAvailableToday: true,
    ),
    const DoctorModel(
      id: 'doc_2',
      name: 'Dr. Marcus Vance',
      specialty: 'Consultant Dermatologist',
      specialtyId: 'dermatology',
      title: 'MD, FAAD - Clinical & Cosmetic Dermatology',
      rating: 4.8,
      reviewCount: 94,
      experienceYears: 9,
      patientCount: 1820,
      consultationFee: 65.0,
      avatarUrl: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. Marcus Vance focuses on chronic dermatological conditions including eczema, psoriasis, acne management, and surgical skin oncology. He believes in combining holistic lifestyle therapy with cutting-edge dermatology.',
      education: 'Stanford University School of Medicine (MD)',
      clinicName: 'Clarity Dermatology & Laser Clinic',
      clinicAddress: '108 West Coast Highway, Suite 210',
      isAvailableToday: true,
    ),
    const DoctorModel(
      id: 'doc_3',
      name: 'Dr. Sophia Rodriguez',
      specialty: 'Neurologist & Sleep Specialist',
      specialtyId: 'neurology',
      title: 'MD, PhD - Neurophysiology',
      rating: 4.9,
      reviewCount: 156,
      experienceYears: 15,
      patientCount: 3100,
      consultationFee: 90.0,
      avatarUrl: 'https://images.unsplash.com/photo-1594824813591-28c9b33a5712?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. Sophia Rodriguez is a globally recognized neurologist with extensive clinical expertise in migraine management, neuromuscular disorders, and clinical sleep architecture.',
      education: 'Columbia University Vagelos College of Physicians and Surgeons',
      clinicName: 'NeuroHealth Comprehensive Care Center',
      clinicAddress: '350 5th Avenue, Suite 1200',
      isAvailableToday: false,
    ),
    const DoctorModel(
      id: 'doc_4',
      name: 'Dr. David Kim',
      specialty: 'Pediatric Specialist',
      specialtyId: 'pediatrics',
      title: 'MD, FAAP - Child & Adolescent Health',
      rating: 4.7,
      reviewCount: 88,
      experienceYears: 8,
      patientCount: 1420,
      consultationFee: 55.0,
      avatarUrl: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. David Kim provides compassionate, family-oriented pediatric care from newborn development to adolescent physical and mental wellness.',
      education: 'UCSF School of Medicine (MD)',
      clinicName: 'Sunrise Pediatrics Center',
      clinicAddress: '88 Bloom Street, Floor 2',
      isAvailableToday: true,
    ),
    const DoctorModel(
      id: 'doc_5',
      name: 'Dr. Olivia Taylor',
      specialty: 'Orthopedic Surgeon',
      specialtyId: 'orthopedics',
      title: 'MD, FAAOS - Sports Medicine & Joint Reconstruction',
      rating: 4.9,
      reviewCount: 210,
      experienceYears: 14,
      patientCount: 2900,
      consultationFee: 85.0,
      avatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. Olivia Taylor specializes in minimally invasive arthroscopic surgery, sports injuries, and joint replacement rehabilitation.',
      education: 'Yale School of Medicine (MD) • Mayo Clinic Fellow',
      clinicName: 'Apex Orthopedic & Sports Medicine',
      clinicAddress: '500 Grand Concourse, Medical Plaza',
      isAvailableToday: true,
    ),
    const DoctorModel(
      id: 'doc_6',
      name: 'Dr. James Wilson',
      specialty: 'Family Medicine Physician',
      specialtyId: 'general',
      title: 'MD - Primary & Preventive Care',
      rating: 4.8,
      reviewCount: 175,
      experienceYears: 11,
      patientCount: 3600,
      consultationFee: 50.0,
      avatarUrl: 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=400&q=80',
      biography: 'Dr. James Wilson offers comprehensive primary healthcare services for adults and seniors, focusing on preventive screening, chronic disease management, and nutrition.',
      education: 'Northwestern University Feinberg School of Medicine',
      clinicName: 'Community Health Partners',
      clinicAddress: '220 Pine Street, Central Wing',
      isAvailableToday: true,
    ),
  ];

  @override
  Future<List<SpecialtyModel>> getSpecialties() async {
    await Future.delayed(const Duration(milliseconds: 200));
    return specialties;
  }

  @override
  Future<List<DoctorModel>> getRecommendedDoctors() async {
    await Future.delayed(const Duration(milliseconds: 250));
    return _mockDoctors.take(4).toList();
  }

  @override
  Future<List<DoctorModel>> getDoctors({
    String? searchQuery,
    String? specialtyId,
    double? minRating,
    double? maxFee,
    bool? onlyAvailableToday,
  }) async {
    await Future.delayed(const Duration(milliseconds: 300));

    return _mockDoctors.where((doc) {
      if (searchQuery != null && searchQuery.trim().isNotEmpty) {
        final query = searchQuery.toLowerCase().trim();
        final matchName = doc.name.toLowerCase().contains(query);
        final matchSpecialty = doc.specialty.toLowerCase().contains(query);
        final matchClinic = doc.clinicName.toLowerCase().contains(query);
        if (!matchName && !matchSpecialty && !matchClinic) return false;
      }

      if (specialtyId != null && specialtyId != 'all' && specialtyId.isNotEmpty) {
        if (doc.specialtyId != specialtyId) return false;
      }

      if (minRating != null && minRating > 0) {
        if (doc.rating < minRating) return false;
      }

      if (maxFee != null && maxFee > 0) {
        if (doc.consultationFee > maxFee) return false;
      }

      if (onlyAvailableToday == true) {
        if (!doc.isAvailableToday) return false;
      }

      return true;
    }).toList();
  }

  @override
  Future<DoctorModel?> getDoctorById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _mockDoctors.firstWhere((d) => d.id == id);
    } catch (_) {
      return null;
    }
  }
}
