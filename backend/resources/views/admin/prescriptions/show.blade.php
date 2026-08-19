@extends('layouts.admin')

@section('title', 'Prescription #' . $prescription->prescription_code)
@section('page_title', 'Prescription Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.prescriptions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Prescriptions
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">Rx: {{ $prescription->prescription_code }}</span>
    </div>

    <!-- Summary Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                <span class="font-mono font-bold text-xl text-slate-900">{{ $prescription->prescription_code }}</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $prescription->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $prescription->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Issued by Dr. {{ $prescription->doctor->user->name ?? 'Doctor' }} on {{ date('F d, Y', strtotime($prescription->prescription_date)) }}</p>
        </div>

        <div class="text-xs text-slate-600">
            <span class="text-slate-400 block font-medium">Patient</span>
            <span class="font-bold text-slate-900 text-sm">{{ $prescription->patient->user->name ?? 'Patient' }}</span>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-semibold text-slate-900 text-sm">Prescribed Medications & Dosages</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Medication Name</th>
                        <th class="py-3 px-6">Dosage & Form</th>
                        <th class="py-3 px-6">Frequency</th>
                        <th class="py-3 px-6">Duration</th>
                        <th class="py-3 px-6">Quantity</th>
                        <th class="py-3 px-6">Refills</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($prescription->items as $item)
                        <tr>
                            <td class="py-3.5 px-6 font-semibold text-slate-900 text-xs">
                                {{ $item->medicine_name }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $item->dosage }} &bull; {{ $item->dosage_form }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 text-xs">No medication items listed.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
