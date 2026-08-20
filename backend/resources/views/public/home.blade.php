@extends('layouts.public')

@section('meta_title', 'HealthCare — Personal Health Monitoring & Doctor Consultation Platform')
@section('meta_description', 'Connect with certified healthcare practitioners, schedule appointments, access your medical records, and track vital health metrics in one secure platform.')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white py-20 lg:py-28">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-cyan-900/30 via-slate-950/0 to-transparent pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
            <!-- Hero Left Content -->
            <div class="space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>Integrated Clinical Platform</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Your Health, <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Connected.</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Seamlessly connect with certified medical specialists, coordinate in-person and teleconsultations, manage electronic health records, and track vital health metrics in real-time.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('public.doctors') }}"
                       class="w-full sm:w-auto px-7 py-3.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl text-sm shadow-lg shadow-cyan-600/30 transition-all text-center">
                        Find a Doctor &rarr;
                    </a>
                    <a href="{{ route('public.how-it-works') }}"
                       class="w-full sm:w-auto px-7 py-3.5 bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold rounded-2xl text-sm transition-all text-center">
                        How It Works
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Role-Based Data Isolation</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Verified Medical Practitioners</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Continuous Biometric Tracking</span>
                    </div>
                </div>
            </div>

            <!-- Hero Right Card Mockup -->
            <div class="relative">
                <div class="relative mx-auto max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl shadow-cyan-950/40 backdrop-blur-sm space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-cyan-600 text-white flex items-center justify-center font-bold">
                                🩺
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-white">Live Clinical Coordination</h3>
                                <p class="text-[11px] text-slate-400">Integrated Medical System</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            ONLINE
                        </span>
                    </div>

                    <!-- Live KPI stats -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-800/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] font-semibold uppercase">Verified Doctors</span>
                            <div class="text-xl font-bold text-white mt-0.5">{{ $totalDoctorsCount }} Specialists</div>
                        </div>
                        <div class="p-3 bg-slate-800/60 rounded-2xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] font-semibold uppercase">Departments</span>
                            <div class="text-xl font-bold text-cyan-400 mt-0.5">{{ $totalSpecialtiesCount }} Specialties</div>
                        </div>
                    </div>

                    <!-- Consultation Mode Indicators -->
                    <div class="space-y-2.5 pt-2">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/40 border border-slate-800 text-xs">
                            <span class="text-slate-300 font-medium">In-Person Appointments</span>
                            <span class="text-emerald-400 font-semibold">Available</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/40 border border-slate-800 text-xs">
                            <span class="text-slate-300 font-medium">Teleconsultation & e-Prescriptions</span>
                            <span class="text-cyan-400 font-semibold">Supported</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/40 border border-slate-800 text-xs">
                            <span class="text-slate-300 font-medium">Remote Biometric Monitoring</span>
                            <span class="text-indigo-400 font-semibold">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Supported Services Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <h2 class="text-xs font-bold text-cyan-600 uppercase tracking-wider">Comprehensive Medical Features</h2>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Built for Patients & Healthcare Providers</h3>
            <p class="text-slate-500 text-sm">
                Every service is backed by strict role-based access control, HIPAA-aware clinical data isolation, and real-time backend synchronization.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-14">
            <!-- 1. Doctor Consultations -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-xl">
                    🩺
                </div>
                <h4 class="font-bold text-slate-900 text-base">Doctor Consultations</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Schedule in-person clinic visits or online teleconsultations with certified specialists across diverse medical disciplines.
                </p>
            </div>

            <!-- 2. Appointment Booking -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl">
                    📅
                </div>
                <h4 class="font-bold text-slate-900 text-base">Appointment Coordination</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Automated booking codes, front-desk check-in queues, shift matrix management, and real-time appointment status updates.
                </p>
            </div>

            <!-- 3. Electronic Medical Records -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl">
                    📋
                </div>
                <h4 class="font-bold text-slate-900 text-base">Electronic Medical Records (EMR)</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Comprehensive visit summaries, symptom logs, primary diagnoses, and physician treatment notes stored securely.
                </p>
            </div>

            <!-- 4. Digital Prescriptions -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl">
                    💊
                </div>
                <h4 class="font-bold text-slate-900 text-base">Digital Prescriptions</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Legitimate e-Prescriptions authored by attending physicians with dosage instructions, refill quantities, and pharmacy guidelines.
                </p>
            </div>

            <!-- 5. Health Metrics Tracking -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xl">
                    ❤️
                </div>
                <h4 class="font-bold text-slate-900 text-base">Continuous Vital Monitoring</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Track vital biomarkers including Blood Pressure, Heart Rate, Blood Oxygen (SpO2), Blood Glucose, and Body Temperature.
                </p>
            </div>

            <!-- 6. Secure Patient Health Profile -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:shadow-lg hover:border-cyan-200 transition-all space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl">
                    🛡️
                </div>
                <h4 class="font-bold text-slate-900 text-base">Patient Profile & Security</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Encrypted emergency contacts, blood type records, allergy information, and strict cryptographic role authorization.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Specialists Section -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-2">
                <h2 class="text-xs font-bold text-cyan-600 uppercase tracking-wider">Certified Practitioners</h2>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Consult with Top Specialists</h3>
            </div>
            <a href="{{ route('public.doctors') }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                View All Doctors Directory &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
            @forelse ($featuredDoctors as $doc)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4 hover:border-cyan-300 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-xs">
                        Dr
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Dr. {{ $doc->user->name ?? 'Specialist' }}</h4>
                        <span class="text-xs font-semibold text-indigo-600 block mt-0.5">{{ $doc->specialty->name ?? 'General Practice' }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">{{ $doc->facility ?? 'Main Medical Center' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                        <span class="font-mono font-bold text-slate-900">${{ number_format($doc->consultation_fee, 2) }}</span>
                        <span class="font-bold text-amber-500">★ {{ number_format($doc->rating_average, 1) }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-4 py-12 text-center text-slate-400 text-xs">
                    No practitioner profiles currently displayed.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 6-Step Workflow Preview -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <h2 class="text-xs font-bold text-cyan-600 uppercase tracking-wider">Patient Journey</h2>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">How HealthCare Works</h3>
            <p class="text-slate-500 text-sm">
                A simple, guided clinical flow designed for fast patient onboarding and continuous care.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mt-14 text-center">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">1</div>
                <h4 class="font-bold text-slate-900 text-xs">Create Account</h4>
                <p class="text-[11px] text-slate-500">Secure patient onboarding and registration</p>
            </div>
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">2</div>
                <h4 class="font-bold text-slate-900 text-xs">Health Profile</h4>
                <p class="text-[11px] text-slate-500">Set blood group, allergies, and emergency info</p>
            </div>
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">3</div>
                <h4 class="font-bold text-slate-900 text-xs">Find Specialist</h4>
                <p class="text-[11px] text-slate-500">Browse certified practitioners by specialty</p>
            </div>
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">4</div>
                <h4 class="font-bold text-slate-900 text-xs">Book Appointment</h4>
                <p class="text-[11px] text-slate-500">Select consultation time slot and mode</p>
            </div>
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">5</div>
                <h4 class="font-bold text-slate-900 text-xs">Doctor Consult</h4>
                <p class="text-[11px] text-slate-500">In-person visit or teleconsultation</p>
            </div>
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white font-bold text-base flex items-center justify-center mx-auto shadow-md">6</div>
                <h4 class="font-bold text-slate-900 text-xs">Care & e-Rx</h4>
                <p class="text-[11px] text-slate-500">Access medical records and digital prescriptions</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-16 bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 text-white border-t border-slate-800">
    <div class="max-w-5xl mx-auto px-4 text-center space-y-6">
        <h3 class="text-2xl sm:text-3xl font-bold tracking-tight">Ready to Connect with Your Healthcare Practitioner?</h3>
        <p class="text-sm text-cyan-200/80 max-w-xl mx-auto leading-relaxed">
            Search our doctor directory, review practitioner credentials, and coordinate your clinical appointments today.
        </p>
        <div class="pt-2">
            <a href="{{ route('public.doctors') }}" class="px-8 py-3.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-2xl text-sm shadow-xl shadow-cyan-600/30 transition-all inline-block">
                Browse Doctors Directory &rarr;
            </a>
        </div>
    </div>
</section>
@endsection
