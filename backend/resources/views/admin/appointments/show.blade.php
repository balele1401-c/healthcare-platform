@extends('layouts.admin')

@section('title', 'Appointment #' . $appointment->booking_code)
@section('page_title', 'Appointment Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button & Breadcrumbs -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Appointments
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">Booking: {{ $appointment->booking_code }}</span>
    </div>

    <!-- Header Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono font-bold text-xl text-slate-900">{{ $appointment->booking_code }}</span>
                @php
                    $badgeStyles = match($appointment->status->value) {
                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeStyles }}">
                    {{ $appointment->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Scheduled for {{ date('l, F d, Y', strtotime($appointment->appointment_date)) }} at {{ substr($appointment->appointment_time, 0, 5) }}</p>
        </div>

        <div class="text-right">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Invoice</p>
            <p class="text-2xl font-bold text-slate-900">${{ number_format($appointment->total_amount, 2) }}</p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Information -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Patient Profile</span>
                <a href="{{ route('admin.patients.show', $appointment->patient->id) }}" class="text-xs text-teal-600 hover:underline">View Full Profile</a>
            </h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block">Name</span>
                    <span class="font-semibold text-slate-800 text-sm">{{ $appointment->patient->user->name ?? 'Patient' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Email / Contact</span>
                    <span class="font-medium text-slate-700">{{ $appointment->patient->user->email ?? 'N/A' }} &bull; {{ $appointment->patient->user->phone ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Blood Type / Allergies</span>
                    <span class="font-medium text-slate-700">{{ $appointment->patient->blood_type ?? 'N/A' }} / {{ $appointment->patient->allergies ?? 'None' }}</span>
                </div>
                @if ($appointment->patient_notes)
                    <div>
                        <span class="text-slate-400 block">Patient Pre-Consultation Notes</span>
                        <p class="mt-1 p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed">{{ $appointment->patient_notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Doctor Information -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Assigned Doctor</span>
                <a href="{{ route('admin.doctors.show', $appointment->doctor->id) }}" class="text-xs text-teal-600 hover:underline">View Doctor Profile</a>
            </h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block">Doctor Name</span>
                    <span class="font-semibold text-slate-800 text-sm">{{ $appointment->doctor->user->name ?? 'Doctor' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Specialty & Facility</span>
                    <span class="font-medium text-teal-700">{{ $appointment->doctor->specialty->name ?? 'General Practice' }}</span>
                    <span class="text-slate-500 block">{{ $appointment->doctor->facility ?? 'Metropolitan Medical Center' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Consultation Type</span>
                    <span class="font-medium text-slate-800">{{ $appointment->consultation_type->label() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Administrative Actions -->
    @if ($appointment->status->value !== 'cancelled' && $appointment->status->value !== 'completed')
        <div class="bg-rose-50/50 rounded-2xl p-6 border border-rose-200 shadow-xs">
            <h3 class="font-semibold text-rose-900 text-sm mb-2">Administrative Cancellation</h3>
            <p class="text-xs text-rose-700 mb-4">If needed due to clinical rescheduling or doctor emergency, you can cancel this appointment. An audit trail event will be recorded.</p>

            <form method="POST" action="{{ route('admin.appointments.cancel', $appointment->id) }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="reason" required placeholder="Provide clinical/administrative reason for cancellation..."
                       class="flex-1 px-3 py-2 text-xs bg-white border border-rose-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500">
                <button type="submit" onclick="return confirm('Are you sure you want to administratively cancel this appointment?');"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-xl text-xs transition-colors shadow-xs">
                    Cancel Appointment
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
