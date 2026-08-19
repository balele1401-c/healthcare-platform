@extends('layouts.doctor')

@section('title', 'Prescription #' . $prescription->prescription_code)
@section('page_title', 'Electronic Prescription (e-Rx) Detail')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.prescriptions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Prescriptions
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">e-Rx: {{ $prescription->prescription_code }}</span>
    </div>

    <!-- Summary Header Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono font-bold text-xl text-slate-900">{{ $prescription->prescription_code }}</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $prescription->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $prescription->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Prescribed on {{ date('l, F d, Y', strtotime($prescription->prescription_date)) }}</p>
        </div>

        <div class="text-xs text-slate-600">
            <span class="text-slate-400 block font-medium">Prescribed For</span>
            <a href="{{ route('doctor.patients.show', $prescription->patient_id) }}" class="font-bold text-slate-900 text-sm hover:text-teal-600">
                {{ $prescription->patient->user->name ?? 'Patient' }} &rarr;
            </a>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm">Prescribed Medications & Dosages</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Medication</th>
                        <th class="py-3 px-6">Dosage & Form</th>
                        <th class="py-3 px-6">Frequency</th>
                        <th class="py-3 px-6">Duration</th>
                        <th class="py-3 px-6">Quantity</th>
                        <th class="py-3 px-6">Refills</th>
                        <th class="py-3 px-6">Instructions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($prescription->items as $item)
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-900 text-xs">
                                {{ $item->medicine->name ?? 'Medicine' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $item->dosage }} &bull; {{ $item->medicine->dosage_form ?? 'Tablet' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $item->frequency }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $item->duration }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-bold text-slate-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                {{ $item->refills_available }} Refills
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                {{ $item->instructions ?? 'Standard as directed' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 text-xs">No medication items listed.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
