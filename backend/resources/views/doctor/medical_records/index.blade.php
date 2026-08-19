@extends('layouts.doctor')

@section('title', 'Clinical Medical Records')
@section('page_title', 'Electronic Medical Records (EMR)')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Clinical Documentation Registry</h2>
            <p class="text-xs text-slate-500">Documented medical records, clinical notes, and vital signs recorded under your practice</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('doctor.medical-records.index') }}" class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-56">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Record #, patient, diagnosis..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-colors">
                    Filter
                </button>
            </form>

            <a href="{{ route('doctor.medical-records.create') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                + New Record
            </a>
        </div>
    </div>

    <!-- Medical Records Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Record #</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Visit Date</th>
                        <th class="py-3.5 px-6">Primary Diagnosis</th>
                        <th class="py-3.5 px-6">Vital Signs</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($medicalRecords as $rec)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ $rec->record_number }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $rec->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $rec->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900 font-medium">
                                {{ date('M d, Y', strtotime($rec->visit_date)) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-800">
                                {{ $rec->diagnosis }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                @if ($rec->vitalSigns)
                                    <span class="font-mono text-[11px] font-semibold text-teal-700 bg-teal-50 px-2 py-0.5 rounded">
                                        BP: {{ $rec->vitalSigns->systolic_blood_pressure }}/{{ $rec->vitalSigns->diastolic_blood_pressure }} &bull; HR: {{ $rec->vitalSigns->heart_rate }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">Not measured</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('doctor.medical-records.show', $rec->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Open Record &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No clinical records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($medicalRecords->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $medicalRecords->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
