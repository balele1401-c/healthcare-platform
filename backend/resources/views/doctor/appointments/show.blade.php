@extends('layouts.doctor')

@section('title', 'Appointment #' . $appointment->booking_code)
@section('page_title', 'Consultation Encounter Chart')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.appointments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Appointments
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">Encounter: {{ $appointment->booking_code }}</span>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-5 gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="font-mono font-bold text-xl text-slate-900">{{ $appointment->booking_code }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ match($appointment->status->value) {
                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'in_consultation' => 'bg-teal-50 text-teal-700 border-teal-200 animate-pulse',
                        'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                    } }}">
                        {{ $appointment->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Consultation Date: {{ date('l, F d, Y', strtotime($appointment->appointment_date)) }} at {{ substr($appointment->appointment_time, 0, 5) }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('doctor.medical-records.create', ['patient_id' => $appointment->patient_id]) }}"
                   class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                    + Document Medical Record
                </a>
                <a href="{{ route('doctor.prescriptions.create', ['patient_id' => $appointment->patient_id]) }}"
                   class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    + Issue Prescription
                </a>
            </div>
        </div>

        <!-- Encounter Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="space-y-4">
                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Patient Profile</span>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $appointment->patient->user->name ?? 'Patient' }}</span>
                    <span class="text-slate-500 block">{{ $appointment->patient->user->email }} &bull; {{ $appointment->patient->user->phone ?? 'No phone' }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 block">Blood Group</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $appointment->patient->blood_type ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Consultation Type</span>
                        <span class="font-semibold text-slate-900">{{ $appointment->consultation_type->label() }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Patient Chief Symptoms</span>
                    <p class="font-medium text-slate-800 mt-1 bg-slate-50 p-3.5 rounded-xl border border-slate-100 leading-relaxed">
                        {{ $appointment->symptoms ?? 'No pre-consultation symptoms entered by patient.' }}
                    </p>
                </div>

                @if ($appointment->cancellation_reason)
                    <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xl text-rose-800 text-xs">
                        <span class="font-bold block">Cancellation Note:</span>
                        <p class="mt-0.5">{{ $appointment->cancellation_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Consultation Status Management -->
        <div class="pt-6 border-t border-slate-100">
            <h4 class="font-bold text-slate-900 text-sm mb-3">Update Consultation Status</h4>
            <form method="POST" action="{{ route('doctor.appointments.status', $appointment->id) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @csrf
                <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="confirmed" {{ $appointment->status->value === 'confirmed' ? 'selected' : '' }}>Confirmed & In-Schedule</option>
                    <option value="completed" {{ $appointment->status->value === 'completed' ? 'selected' : '' }}>Completed (Encounter Finished)</option>
                    <option value="cancelled" {{ $appointment->status->value === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <input type="text" name="notes" placeholder="Optional clinical note / cancellation reason"
                       class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors shadow-xs">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
