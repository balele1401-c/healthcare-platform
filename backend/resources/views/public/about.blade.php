@extends('layouts.public')

@section('meta_title', 'About HealthCare — Clinical Mission & Platform Architecture')
@section('meta_description', 'Learn about HealthCare Platform mission to provide integrated, secure, and accessible clinical consultation and health tracking tools.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Heading -->
        <div class="text-center space-y-4 max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-semibold">
                <span>About Our Platform</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                Empowering Patients & Practitioners Through Connected Care
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                HealthCare Integrated Medical Platform is designed to bridge the gap between patient self-monitoring and clinical physician encounters through modern, secure, and intuitive digital health tools.
            </p>
        </div>

        <!-- 3 Pillars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-xl">
                    🎯
                </div>
                <h3 class="font-bold text-slate-900 text-base">Our Mission</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    To provide accessible, dependable, and unified healthcare management where patients have full ownership of their health journey and clinicians have real-time visibility.
                </p>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl">
                    🔒
                </div>
                <h3 class="font-bold text-slate-900 text-base">Clinical Data Privacy</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Zero compromise on medical confidentiality. Strict role-based access control guarantees that electronic medical records and prescriptions are strictly doctor-patient bound.
                </p>
            </div>

            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl">
                    ⚡
                </div>
                <h3 class="font-bold text-slate-900 text-base">Operational Agility</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Designed for fast front-desk coordination, streamlined doctor consultation queues, and seamless cross-platform mobile and web access.
                </p>
            </div>
        </div>

        <!-- System Architecture & Technology Overview -->
        <div class="bg-slate-900 rounded-3xl p-8 sm:p-12 text-white space-y-6">
            <h2 class="text-2xl font-bold tracking-tight">Built on Proven Enterprise Architecture</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                HealthCare leverages a distributed multi-tier architecture featuring a high-performance Laravel backend, PostgreSQL relational database, and responsive multi-platform Flutter and web interfaces.
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-800 text-xs">
                <div>
                    <span class="text-cyan-400 font-bold block text-base">REST API v1</span>
                    <span class="text-slate-400">Standardized Endpoints</span>
                </div>
                <div>
                    <span class="text-indigo-400 font-bold block text-base">PostgreSQL</span>
                    <span class="text-slate-400">Relational Database</span>
                </div>
                <div>
                    <span class="text-emerald-400 font-bold block text-base">RBAC Security</span>
                    <span class="text-slate-400">4 Isolated Roles</span>
                </div>
                <div>
                    <span class="text-amber-400 font-bold block text-base">Audit Trail</span>
                    <span class="text-slate-400">Live Operation Logs</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
