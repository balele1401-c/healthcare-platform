@extends('layouts.admin')

@section('title', 'Patient Details — #' . $patient->id)
@section('page_title', 'Patient Profile Details')

@section('content')
<div class="space-y-6">
    <!-- Top Action / Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.patients.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Patients List
        </a>
        <span class="text-xs text-slate-400">Patient ID: #{{ $patient->id }}</span>
    </div>

    <!-- Patient Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-2xl shadow-md shadow-teal-600/30 flex-shrink-0">
                {{ strtoupper(substr($patient->user->name ?? 'P', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $patient->user->name ?? 'Patient Name' }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                    <span>{{ $patient->user->email ?? 'No email' }}</span>
                    <span>&bull;</span>
                    <span>{{ $patient->user->phone ?? 'No phone' }}</span>
                    <span>&bull;</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-medium">Active Patient</span>
                </div>
            </div>
        </div>

        <!-- Vitals Quick Overview -->
        <div class="flex items-center gap-4 border-t sm:border-t-0 sm:border-l border-slate-100 pt-4 sm:pt-0 sm:pl-6 w-full sm:w-auto">
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Blood Group</p>
                <p class="text-lg font-bold text-rose-600">{{ $patient->blood_type ?? 'N/A' }}</p>
            </div>
            <div class="border-l border-slate-100 pl-4">
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Height / Weight</p>
                <p class="text-sm font-semibold text-slate-800">{{ $patient->height ?? '—' }} cm / {{ $patient->weight ?? '—' }} kg</p>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Demographic & Emergency Contacts -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3">Demographic & Emergency Info</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block">Date of Birth</span>
                    <span class="font-medium text-slate-800">{{ $patient->date_of_birth ? date('F d, Y', strtotime($patient->date_of_birth)) : 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Residential Address</span>
                    <span class="font-medium text-slate-800">{{ $patient->address ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Emergency Contact</span>
                    <span class="font-medium text-slate-800">{{ $patient->emergency_contact_name ?? 'None listed' }}</span>
                    @if ($patient->emergency_contact_phone)
                        <span class="text-slate-500 block mt-0.5">({{ $patient->emergency_contact_phone }})</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 block">Allergies Listed</span>
                    <span class="font-medium text-rose-700 bg-rose-50 px-2 py-0.5 rounded inline-block mt-0.5">{{ $patient->allergies ?? 'No known allergies' }}</span>
                </div>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3">Consultation & Appointment History</h3>

            <div class="divide-y divide-slate-100">
                @forelse ($patient->appointments as $apt)
                    <div class="py-3 flex items-center justify-between gap-4 text-xs">
                        <div>
                            <div class="font-mono font-medium text-slate-900">{{ $apt->booking_code }}</div>
                            <div class="text-slate-500 mt-0.5">With {{ $apt->doctor->user->name ?? 'Doctor' }} &bull; {{ date('M d, Y', strtotime($apt->appointment_date)) }} ({{ substr($apt->appointment_time, 0, 5) }})</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded-full font-medium {{ $apt->status->value === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $apt->status->label() }}
                            </span>
                            <a href="{{ route('admin.appointments.show', $apt->id) }}" class="text-teal-600 font-semibold hover:underline">View</a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No appointments booked by this patient yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
