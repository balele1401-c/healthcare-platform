@extends('layouts.public')

@section('meta_title', 'How HealthCare Works — Patient Onboarding & Clinical Steps')
@section('meta_description', 'Learn how to register, find a specialist doctor, book clinical appointments, and manage medical prescriptions on HealthCare.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Heading -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-semibold">
                <span>Guided Patient Flow</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                How HealthCare Works
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                A straightforward, secure clinical process designed to get you the care you need with complete digital records and seamless follow-ups.
            </p>
        </div>

        <!-- 6 Steps Vertical Timeline / Cards -->
        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    1
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Create Your Account</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Sign up via our mobile application or web portal with your email and contact information. Your credentials are protected with industry-standard bcrypt hashing and rate-limited authentication.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    2
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Complete Your Emergency & Health Profile</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Record essential clinical parameters such as your blood group, known drug allergies, and emergency contact details to ensure attending physicians have immediate life-critical context.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    3
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Browse Specialists & Departments</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Explore our verified doctor directory filtered by medical specialties (Cardiology, Dermatology, Pediatrics, General Practice, Neurology, and more). Review practitioner credentials, consultation rates, and ratings.
                    </p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    4
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Select Consultation Slot & Book</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Choose your preferred date, shift time slot, and consultation mode (In-Person Clinic Visit or Online Teleconsultation). Receive a unique booking confirmation code instantly.
                    </p>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    5
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Consult with Your Doctor</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Attend your scheduled consultation. The attending doctor reviews your symptom timeline, records vital signs, documents diagnoses in your electronic health chart, and formulates your treatment plan.
                    </p>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="flex flex-col sm:flex-row items-start gap-6 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md">
                    6
                </div>
                <div class="space-y-1.5">
                    <h3 class="font-bold text-slate-900 text-lg">Access Medical Records & Electronic Prescriptions</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        View clinical consultation summaries, review prescribed medication dosages and refill instructions, and monitor your vital biomarker recovery trends anytime from your patient dashboard.
                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Transparency Notice (Compliance & Mayar Readiness) -->
        <div class="p-6 rounded-3xl bg-slate-100 border border-slate-200 text-xs text-slate-600 space-y-2">
            <h4 class="font-bold text-slate-900 text-xs flex items-center gap-2">
                <span>💳 Financial Settlement & Payment Notice</span>
            </h4>
            <p class="leading-relaxed">
                Consultation fees and electronic billing capabilities are currently configured in demonstration / sandbox mode pending full institutional payment gateway onboarding. No live financial charges are processed during this staging period.
            </p>
        </div>
    </div>
</div>
@endsection
