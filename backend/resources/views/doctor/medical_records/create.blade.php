@extends('layouts.doctor')

@section('title', 'Document Medical Record')
@section('page_title', 'Document Clinical Encounter Record')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.medical-records.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Medical Records
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl p-4 space-y-1">
            <span class="font-bold block">Please address the following validation errors:</span>
            @foreach ($errors->all() as $err)
                <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.medical-records.store') }}" class="space-y-6">
        @csrf

        <!-- Clinical Encounter Details Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Patient & Encounter Basics</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="patient_id" class="block text-xs font-semibold text-slate-700">Patient *</label>
                    <select id="patient_id" name="patient_id" required
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Select an assigned patient...</option>
                        @foreach ($patients as $pat)
                            <option value="{{ $pat->id }}" {{ old('patient_id', $patientId) == $pat->id ? 'selected' : '' }}>
                                {{ $pat->user->name }} (ID: #PAT-{{ $pat->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="visit_date" class="block text-xs font-semibold text-slate-700">Consultation / Visit Date *</label>
                    <input type="date" id="visit_date" name="visit_date" required value="{{ old('visit_date', date('Y-m-d')) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="chief_complaint" class="block text-xs font-semibold text-slate-700">Chief Complaint *</label>
                <input type="text" id="chief_complaint" name="chief_complaint" required value="{{ old('chief_complaint') }}"
                       placeholder="e.g. Severe throbbing headache for 3 days with photophobia"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="space-y-1.5">
                <label for="symptoms" class="block text-xs font-semibold text-slate-700">Reported Symptoms & Onset</label>
                <textarea id="symptoms" name="symptoms" rows="3"
                          placeholder="Detailed symptom description and patient report"
                          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('symptoms') }}</textarea>
            </div>
        </div>

        <!-- Clinical Diagnosis & Treatment Plan Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Diagnosis & Plan</h3>

            <div class="space-y-1.5">
                <label for="diagnosis" class="block text-xs font-semibold text-slate-700">Clinical Diagnosis *</label>
                <input type="text" id="diagnosis" name="diagnosis" required value="{{ old('diagnosis') }}"
                       placeholder="e.g. Acute Migraine without Aura (ICD-10 G43.0)"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="space-y-1.5">
                <label for="treatment" class="block text-xs font-semibold text-slate-700">Prescribed Treatment & Clinical Interventions</label>
                <textarea id="treatment" name="treatment" rows="3"
                          placeholder="e.g. Prescribed Sumatriptan 50mg PO PRN, hydration, rest in dark room"
                          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('treatment') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="follow_up_date" class="block text-xs font-semibold text-slate-700">Recommended Follow-Up Date</label>
                    <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="clinical_notes" class="block text-xs font-semibold text-slate-700">Confidential Practitioner Notes</label>
                    <input type="text" id="clinical_notes" name="clinical_notes" value="{{ old('clinical_notes') }}"
                           placeholder="Internal observations"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- Vital Signs Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Encounter Vital Signs (Optional)</h3>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Systolic (mmHg)</label>
                    <input type="number" name="systolic" value="{{ old('systolic') }}" placeholder="120"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Diastolic (mmHg)</label>
                    <input type="number" name="diastolic" value="{{ old('diastolic') }}" placeholder="80"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Heart Rate (bpm)</label>
                    <input type="number" name="heart_rate" value="{{ old('heart_rate') }}" placeholder="72"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">Temp (°C)</label>
                    <input type="number" step="0.1" name="body_temperature" value="{{ old('body_temperature') }}" placeholder="36.8"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-600">SpO2 (%)</label>
                    <input type="number" name="blood_oxygen" value="{{ old('blood_oxygen') }}" placeholder="98"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('doctor.medical-records.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-all shadow-md shadow-teal-600/30">
                Save & Sign Medical Record
            </button>
        </div>
    </form>
</div>
@endsection
