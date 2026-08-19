@extends('layouts.admin')

@section('title', 'Medical Records Overview')
@section('page_title', 'Medical Records Overview')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Clinical Medical Records</h2>
            <p class="text-xs text-slate-500">Administrative registry of documented patient visits and clinical consultations</p>
        </div>

        <form method="GET" action="{{ route('admin.medical-records.index') }}" class="flex items-center gap-2">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search record #, patient, diagnosis..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search)
                <a href="{{ route('admin.medical-records.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Medical Records Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Record #</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Visit Date</th>
                        <th class="py-3.5 px-6">Primary Diagnosis</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($medicalRecords as $record)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-semibold text-slate-900 text-xs">
                                {{ $record->record_number }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $record->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $record->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $record->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-teal-600 font-medium">{{ $record->doctor->specialty->name ?? 'General' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900 font-medium">
                                {{ date('M d, Y', strtotime($record->visit_date)) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                <span class="font-medium text-slate-900">{{ $record->diagnosis ?? 'General Consultation' }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.medical-records.show', $record->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Summary &rarr;
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
