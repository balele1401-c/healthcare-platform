@extends('layouts.doctor')

@section('title', 'Issue Digital Prescription')
@section('page_title', 'Issue Electronic Prescription (e-Rx)')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.prescriptions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Prescriptions
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl p-4 space-y-1">
            <span class="font-bold block">Please fix the following validation errors:</span>
            @foreach ($errors->all() as $err)
                <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.prescriptions.store') }}" class="space-y-6">
        @csrf

        <!-- Prescription Header Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3">Prescription Target Patient</h3>

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
                    <label for="prescription_date" class="block text-xs font-semibold text-slate-700">Prescription Date *</label>
                    <input type="date" id="prescription_date" name="prescription_date" required value="{{ old('prescription_date', date('Y-m-d')) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block text-xs font-semibold text-slate-700">Pharmacy Instructions & Precautions</label>
                <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                       placeholder="e.g. Take with food. Discontinue if rash occurs."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>
        </div>

        <!-- Prescribed Medications Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-base">Medication Line Items</h3>
                <span class="text-xs text-slate-500">Add medications with precise clinical dosage instructions</span>
            </div>

            <!-- Medication Item 0 -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Medicine Name *</label>
                        <input type="text" name="items[0][medicine_name]" required value="{{ old('items.0.medicine_name', 'Amoxicillin') }}"
                               placeholder="e.g. Amoxicillin" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Dosage *</label>
                        <input type="text" name="items[0][dosage]" required value="{{ old('items.0.dosage', '500mg') }}"
                               placeholder="e.g. 500mg" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Form *</label>
                        <input type="text" name="items[0][dosage_form]" required value="{{ old('items.0.dosage_form', 'Capsule') }}"
                               placeholder="e.g. Capsule, Tablet, Syrup" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Frequency *</label>
                        <input type="text" name="items[0][frequency]" required value="{{ old('items.0.frequency', '3 times a day') }}"
                               placeholder="e.g. 3 times daily" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Duration *</label>
                        <input type="text" name="items[0][duration]" required value="{{ old('items.0.duration', '7 days') }}"
                               placeholder="e.g. 7 days" class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Quantity *</label>
                        <input type="number" name="items[0][quantity]" required min="1" value="{{ old('items.0.quantity', 21) }}"
                               class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700">Refills</label>
                        <input type="number" name="items[0][refills_available]" min="0" value="{{ old('items.0.refills_available', 0) }}"
                               class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-slate-700">Patient Dosage Instructions</label>
                    <input type="text" name="items[0][instructions]" value="{{ old('items.0.instructions', 'Take after meals with water.') }}"
                           placeholder="e.g. Take 1 capsule after meals with water"
                           class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3 py-2">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('doctor.prescriptions.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-all shadow-md shadow-teal-600/30">
                Sign & Transmit Electronic Prescription
            </button>
        </div>
    </form>
</div>
@endsection
