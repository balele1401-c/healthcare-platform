@extends('layouts.doctor')

@section('title', 'Medical Record #' . $medicalRecord->record_number)
@section('page_title', 'Clinical Encounter Record Detail')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.medical-records.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Medical Records
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">EMR: {{ $medicalRecord->record_number }}</span>
    </div>

    <!-- Main Clinical Record Summary -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-5 gap-4">
            <div>
                <span class="font-mono font-bold text-xl text-slate-900">{{ $medicalRecord->record_number }}</span>
                <p class="text-xs text-slate-500 mt-1">Encounter Date: {{ date('l, F d, Y', strtotime($medicalRecord->visit_date)) }} &bull; {{ $medicalRecord->facility }}</p>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-400 block font-medium">Patient</span>
                <a href="{{ route('doctor.patients.show', $medicalRecord->patient_id) }}" class="font-bold text-slate-900 text-sm hover:text-teal-600">
                    {{ $medicalRecord->patient->user->name ?? 'Patient' }} &rarr;
                </a>
            </div>
        </div>

        <!-- Clinical Narrative -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="space-y-4">
                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Chief Complaint</span>
                    <p class="font-semibold text-slate-900 text-sm mt-0.5">{{ $medicalRecord->chief_complaint }}</p>
                </div>

                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Symptoms & Patient Report</span>
                    <p class="font-medium text-slate-700 mt-0.5 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                        {{ $medicalRecord->symptoms ?? 'None documented.' }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Primary Diagnosis</span>
                    <p class="font-bold text-teal-700 text-sm mt-0.5 bg-teal-50 px-3 py-1.5 rounded-xl border border-teal-200 inline-block">
                        {{ $medicalRecord->diagnosis }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Treatment Plan & Interventions</span>
                    <p class="font-medium text-slate-700 mt-0.5 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                        {{ $medicalRecord->treatment ?? 'No medications or procedural interventions entered.' }}
                    </p>
                </div>

                @if ($medicalRecord->follow_up_date)
                    <div>
                        <span class="text-slate-400 block font-medium uppercase tracking-wider text-[10px]">Follow-Up Scheduled</span>
                        <span class="font-semibold text-slate-900">{{ date('F d, Y', strtotime($medicalRecord->follow_up_date)) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Vital Signs Strip -->
        @if ($medicalRecord->vitalSigns)
            <div class="pt-6 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Encounter Vital Signs</h4>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Blood Pressure</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $medicalRecord->vitalSigns->systolic_blood_pressure }}/{{ $medicalRecord->vitalSigns->diastolic_blood_pressure }} mmHg</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Heart Rate</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $medicalRecord->vitalSigns->heart_rate }} bpm</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Body Temp</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $medicalRecord->vitalSigns->body_temperature }} °C</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Oxygen SpO2</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $medicalRecord->vitalSigns->blood_oxygen }} %</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Measured</span>
                        <span class="font-semibold text-slate-700 text-xs">{{ $medicalRecord->vitalSigns->measured_at ? date('H:i', strtotime($medicalRecord->vitalSigns->measured_at)) : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
