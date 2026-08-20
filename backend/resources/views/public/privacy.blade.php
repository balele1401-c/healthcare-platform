@extends('layouts.public')

@section('meta_title', 'Privacy Policy — HealthCare Integrated Medical Platform')
@section('meta_description', 'Review the HealthCare data protection, HIPAA-aware clinical confidentiality, and personal information handling privacy policy.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="border-b border-slate-200 pb-6 space-y-2">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Privacy & Data Protection Policy</h1>
            <p class="text-xs text-slate-500">Effective Date: August 20, 2026 &bull; Version 1.0</p>
        </div>

        <div class="prose prose-slate max-w-none text-xs leading-relaxed text-slate-600 space-y-6">
            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">1. Information We Collect</h2>
                <p>
                    HealthCare Integrated Medical Platform collects information necessary to provide clinical appointment coordination, telemedicine consultations, and personal health metrics tracking. This includes account credentials (name, email, phone), emergency contact details, blood group, vital biomarker logs (heart rate, blood pressure, SpO2, glucose, temperature), and clinical visit records documented during consultations.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">2. How We Protect Clinical Data</h2>
                <p>
                    Clinical health information is subject to strict role-based access control (RBAC). Attending medical practitioners can only view records of patients under their clinical care. Administrative and operational staff are restricted to operational demographic data and are strictly barred from reading or modifying clinical diagnoses, medical progress notes, or prescriptions.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">3. Third-Party Sharing & Disclosure</h2>
                <p>
                    We do not sell, rent, or trade patient personal or clinical health data. Data is only accessible to verified practitioners assigned to your consultation shifts or as required by law for public health safety compliance.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">4. Data Retention & Patient Rights</h2>
                <p>
                    Patients have the right to inspect their health profile, export encounter summaries, and request profile corrections through the platform's profile management endpoints.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection
