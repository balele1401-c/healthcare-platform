@extends('layouts.staff')

@section('title', 'Appointment #' . $appointment->booking_code)
@section('page_title', 'Appointment Coordination — #' . $appointment->booking_code)

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- Back button & Status Banner -->
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.appointments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Back to Appointment Coordination Queue
        </a>

        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ match($appointment->status->value) {
            'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'in_consultation' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        } }}">
            Status: {{ $appointment->status->label() }}
        </span>
    </div>

    <!-- 2 Column Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Patient Operational Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Patient Demographic Details</h3>
                <a href="{{ route('staff.patients.show', $appointment->patient_id) }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                    Patient Profile &rarr;
                </a>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-cyan-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                    {{ strtoupper(substr($appointment->patient->user->name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">{{ $appointment->patient->user->name ?? 'Patient' }}</h4>
                    <p class="text-xs text-slate-500">{{ $appointment->patient->user->email ?? 'No email' }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-3 text-xs pt-2">
                <div>
                    <dt class="text-slate-400 font-medium">Contact Phone</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->patient->user->phone ?? 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Blood Group</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->patient->blood_type ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Emergency Contact</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->patient->emergency_contact_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Emergency Phone</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->patient->emergency_contact_phone ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Assigned Doctor & Department -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Assigned Practitioner</h3>
                <a href="{{ route('staff.doctors.show', $appointment->doctor_id) }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                    Doctor Schedule &rarr;
                </a>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                    Dr
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Dr. {{ $appointment->doctor->user->name ?? 'Doctor' }}</h4>
                    <p class="text-xs text-indigo-600 font-semibold">{{ $appointment->doctor->specialty->name ?? 'General Practice' }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-3 text-xs pt-2">
                <div>
                    <dt class="text-slate-400 font-medium">Consultation Type</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->consultation_type->label() }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Medical Facility</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ $appointment->doctor->facility ?? 'Main Clinic' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Consultation Date</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ date('l, M d, Y', strtotime($appointment->appointment_date)) }}</dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Scheduled Time</dt>
                    <dd class="font-semibold text-slate-900 mt-0.5">{{ substr($appointment->appointment_time, 0, 5) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Billing & Reason -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Billing Details -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Billing & Settlement</h3>
            @if ($appointment->payment)
                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="text-slate-500">Transaction ID:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $appointment->payment->payment_reference }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Invoice Amount:</span>
                    <span class="text-base font-bold text-slate-900">${{ number_format($appointment->payment->amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Payment Status:</span>
                    <span class="font-semibold uppercase {{ ($appointment->payment->status->value ?? $appointment->payment->status) === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ is_object($appointment->payment->status) ? $appointment->payment->status->label() : $appointment->payment->status }}
                    </span>
                </div>
            @else
                <p class="text-xs text-slate-400 py-3">No billing invoice recorded for this appointment yet.</p>
            @endif
        </div>

        <!-- Clinical Privacy Notice -->
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 space-y-2">
            <div class="flex items-center gap-2 text-slate-700 font-semibold text-xs">
                <svg class="w-4 h-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Clinical Confidentiality Policy</span>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                As front-desk and operational staff, patient diagnoses, clinical examination notes, and medical prescriptions are restricted to attending physicians under HIPAA/medical data isolation rules.
            </p>
        </div>
    </div>

    <!-- Status Coordination Actions -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-sm">Front-Desk Coordination Action</h3>
        <p class="text-xs text-slate-500">Confirm booking for patient check-in or cancel booking upon patient request.</p>

        <form method="POST" action="{{ route('staff.appointments.status', $appointment->id) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Set New Coordination Status</label>
                    <select id="status" name="status" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <option value="confirmed" {{ $appointment->status->value === 'confirmed' ? 'selected' : '' }}>Confirm & Check-In Patient</option>
                        <option value="cancelled" {{ $appointment->status->value === 'cancelled' ? 'selected' : '' }}>Cancel Appointment</option>
                    </select>
                </div>

                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 mb-1">Coordination Note / Cancellation Reason</label>
                    <input type="text" id="reason" name="reason" placeholder="e.g., Patient confirmed via phone"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold transition-colors shadow-xs">
                Update Booking Status
            </button>
        </form>
    </div>
</div>
@endsection
