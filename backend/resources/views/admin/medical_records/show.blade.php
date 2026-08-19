@extends('layouts.admin')

@section('title', 'Medical Record #' . $medicalRecord->record_number)
@section('page_title', 'Clinical Record Summary')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.medical-records.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Medical Records
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">Record: {{ $medicalRecord->record_number }}</span>
    </div>

    <!-- Main Summary Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-4">
            <div>
                <span class="font-mono font-bold text-lg text-slate-900">{{ $medicalRecord->record_number }}</span>
                <p class="text-xs text-slate-500 mt-0.5">Visit Date: {{ date('F d, Y', strtotime($medicalRecord->visit_date)) }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                    Documented by {{ $medicalRecord->doctor->user->name ?? 'Doctor' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="space-y-3">
                <div>
                    <span class="text-slate-400 block font-medium">Patient</span>
                    <span class="font-semibold text-slate-800 text-sm">{{ $medicalRecord->patient->user->name ?? 'Patient' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Chief Complaint</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $medicalRecord->chief_complaint ?? 'None documented' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Symptoms</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $medicalRecord->symptoms ?? 'None documented' }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <span class="text-slate-400 block font-medium">Diagnosis</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $medicalRecord->diagnosis ?? 'Routine Consultation' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Prescribed Treatment</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $medicalRecord->treatment ?? 'None documented' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Follow-Up Instructions</span>
                    <p class="font-medium text-slate-800 mt-0.5">{{ $medicalRecord->follow_up_instructions ?? 'As needed' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
