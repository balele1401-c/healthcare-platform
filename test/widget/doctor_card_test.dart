import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/doctor/domain/models/doctor_model.dart';
import 'package:healthcare/features/doctor/presentation/widgets/doctor_card.dart';

void main() {
  const testDoctor = DoctorModel(
    id: 'doc_1',
    name: 'Dr. Emily Chen',
    specialty: 'Senior Cardiologist',
    specialtyId: 'cardiology',
    title: 'MD, FACC',
    rating: 4.9,
    reviewCount: 128,
    experienceYears: 12,
    patientCount: 2450,
    consultationFee: 75.0,
    avatarUrl: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
    biography: 'Specialist bio',
    education: 'Harvard Medical School',
    clinicName: 'Metropolitan Heart Institute',
    clinicAddress: '742 Evergreen Terrace',
  );

  testWidgets('DoctorCard displays doctor details accurately', (tester) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: DoctorCard(doctor: testDoctor),
        ),
      ),
    );

    expect(find.text('Dr. Emily Chen'), findsOneWidget);
    expect(find.text('Senior Cardiologist'), findsOneWidget);
    expect(find.text('Metropolitan Heart Institute'), findsOneWidget);
    expect(find.text('4.9'), findsOneWidget);
    expect(find.text('\$75'), findsOneWidget);
    expect(find.text('Book Visit'), findsOneWidget);
  });
}
