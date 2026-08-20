@extends('layouts.public')

@section('meta_title', 'Clinical & Platform Services — HealthCare')
@section('meta_description', 'Explore HealthCare supported medical services: doctor consultations, electronic medical records, digital prescriptions, and vital biomarker tracking.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-semibold">
                <span>Clinical & Operational Capabilities</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                Our Supported Medical Services
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Explore our full suite of telemedicine, in-clinic coordination, electronic medical records, and digital health tracking features.
            </p>
        </div>

        <!-- 6 Detailed Service Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- 1. Doctor Consultation -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-2xl">
                    🩺
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Doctor Consultation</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Connect directly with certified practitioners. Choose between in-person clinic visits or online teleconsultations tailored to your schedule and clinical needs.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Specialist matching by department</li>
                    <li class="flex items-center gap-2">&bull; In-person & online consultation modes</li>
                    <li class="flex items-center gap-2">&bull; Real-time status coordination</li>
                </ul>
            </div>

            <!-- 2. Appointment Booking -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                    📅
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Appointment Booking & Queue</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Smart booking engine providing automated booking codes, time-slot selection, queue management, and instant rescheduling capabilities.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Unique booking verification codes</li>
                    <li class="flex items-center gap-2">&bull; Front-desk operational check-in</li>
                    <li class="flex items-center gap-2">&bull; Automated shift conflict prevention</li>
                </ul>
            </div>

            <!-- 3. Electronic Medical Records -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-2xl">
                    📋
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Electronic Medical Records (EMR)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Secure clinical encounter documentation capturing chief complaints, symptom history, primary diagnoses, physician treatment plans, and follow-up directives.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Comprehensive encounter history</li>
                    <li class="flex items-center gap-2">&bull; Integrated vital sign logs at visit</li>
                    <li class="flex items-center gap-2">&bull; Patient & attending doctor access only</li>
                </ul>
            </div>

            <!-- 4. Digital Prescriptions -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-2xl">
                    💊
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Electronic Prescriptions (e-Rx)</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Digital prescription issuance directly linked to official medicine catalog entries with precise dosage forms, frequency, duration, and refill counts.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Unique Rx tracking codes</li>
                    <li class="flex items-center gap-2">&bull; Multi-medication line items</li>
                    <li class="flex items-center gap-2">&bull; Patient digital access anytime</li>
                </ul>
            </div>

            <!-- 5. Continuous Vital Signs Tracking -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-2xl">
                    ❤️
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Remote Health Metric Monitoring</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Track vital biomarkers continuously to help attending clinicians review physiological trends and adjust care plans effectively.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Blood Pressure & Heart Rate</li>
                    <li class="flex items-center gap-2">&bull; Blood Oxygen (SpO2) & Glucose</li>
                    <li class="flex items-center gap-2">&bull; Body Temperature & BMI calculations</li>
                </ul>
            </div>

            <!-- 6. Patient Profile & Intake -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4 hover:border-cyan-300 transition-colors">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-2xl">
                    🛡️
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Patient Profile & Front-Desk Intake</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Centralized demographic management supporting staff-assisted in-clinic intake, emergency contacts, blood group logging, and allergy declarations.
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/60">
                    <li class="flex items-center gap-2">&bull; Front-desk patient onboarding</li>
                    <li class="flex items-center gap-2">&bull; Emergency contact repository</li>
                    <li class="flex items-center gap-2">&bull; Immutable audit logging</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
