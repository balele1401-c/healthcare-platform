@extends('layouts.public')

@section('meta_title', 'Terms of Service — HealthCare Integrated Medical Platform')
@section('meta_description', 'Read the terms and conditions governing access and usage of the HealthCare Integrated Medical Platform.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="border-b border-slate-200 pb-6 space-y-2">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Terms of Service</h1>
            <p class="text-xs text-slate-500">Effective Date: August 20, 2026 &bull; Version 1.0</p>
        </div>

        <div class="prose prose-slate max-w-none text-xs leading-relaxed text-slate-600 space-y-6">
            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">1. Acceptance of Terms</h2>
                <p>
                    By accessing or using HealthCare Integrated Medical Platform, you agree to comply with and be bound by these Terms of Service. If you do not agree to these terms, please do not use the service.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">2. Medical Consultation Disclaimer</h2>
                <p>
                    HealthCare is a clinical coordination, appointment scheduling, and electronic health information management platform. Digital health metrics and self-logged vitals do not constitute automated medical diagnoses. Always follow the explicit clinical counsel of licensed healthcare providers.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">3. Emergency Situations</h2>
                <p>
                    This platform is not designed for life-threatening emergencies. In case of acute cardiac symptoms, severe trauma, or medical emergencies, contact your local emergency response services immediately.
                </p>
            </section>

            <section class="space-y-2">
                <h2 class="text-base font-bold text-slate-900">4. Billing & Demonstration Mode</h2>
                <p>
                    Consultation fee displays and electronic billing statuses operate under demonstration and staging readiness mode until final payment gateway onboarding is completed.
                </p>
            </section>
        </div>
    </div>
</div>
@endsection
