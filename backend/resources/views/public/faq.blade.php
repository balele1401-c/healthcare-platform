@extends('layouts.public')

@section('meta_title', 'Frequently Asked Questions (FAQ) — HealthCare')
@section('meta_description', 'Find answers to common questions regarding doctor appointments, teleconsultations, electronic medical records, and data privacy on HealthCare.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-semibold">
                <span>Frequently Asked Questions</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Everything You Need to Know
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
                Got questions about scheduling consultations, finding specialists, accessing your medical records, or platform security? Explore our answers below.
            </p>
        </div>

        <!-- FAQ Categories -->
        <div class="space-y-10">
            <!-- 1. Appointments & Consultations -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <span>🩺 Appointments & Consultations</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">How do I book an appointment with a doctor?</h4>
                        <p class="text-slate-600 leading-relaxed">
                            You can browse our Doctor Directory, filter by medical specialty, view practitioner profiles, and select an available consultation shift. Once booked, you receive an automated booking code.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">What is the difference between In-Person and Online Teleconsultation?</h4>
                        <p class="text-slate-600 leading-relaxed">
                            In-person appointments take place at the affiliated clinic or hospital facility. Online teleconsultations take place digitally via our secure teleconsultation chat channels.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Medical Records & Prescriptions -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <span>📋 Medical Records & e-Prescriptions</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">Who can view my Electronic Medical Records (EMR)?</h4>
                        <p class="text-slate-600 leading-relaxed">
                            Only you and the attending physician assigned to your care have access to your clinical records and diagnoses. Administrative and front-desk staff have demographic-only access.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">How do digital prescriptions (e-Rx) work?</h4>
                        <p class="text-slate-600 leading-relaxed">
                            Following your clinical consultation, your doctor issues a digital prescription directly in the system containing medication names, dosage strength, frequency, and refill allowance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. Privacy & Security -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                    <span>🔒 Privacy & Security</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <h4 class="font-bold text-slate-900 text-sm">How is patient data protected?</h4>
                        <p class="text-slate-600 leading-relaxed">
                            All passwords and private credentials are cryptographically protected using Bcrypt. APIs are token-authenticated with Sanctum, and cross-patient isolation policies prevent unauthorized record exposure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
