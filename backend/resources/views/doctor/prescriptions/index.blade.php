@extends('layouts.doctor')

@section('title', 'Digital Prescriptions')
@section('page_title', 'Electronic Prescriptions (e-Rx)')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Issued Electronic Prescriptions</h2>
            <p class="text-xs text-slate-500">Authorized medication orders and refill permissions issued to your patients</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" action="{{ route('doctor.prescriptions.index') }}" class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-56">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Code, patient name..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-colors">
                    Filter
                </button>
            </form>

            <a href="{{ route('doctor.prescriptions.create') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                + Issue Prescription
            </a>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Rx Code</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Issue Date</th>
                        <th class="py-3.5 px-6">Medications</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($prescriptions as $rx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ $rx->prescription_code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $rx->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $rx->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900 font-medium">
                                {{ date('M d, Y', strtotime($rx->prescription_date)) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-800">
                                {{ $rx->items->count() }} {{ Str::plural('Item', $rx->items->count()) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $rx->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $rx->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('doctor.prescriptions.show', $rx->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Details &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No electronic prescriptions issued.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($prescriptions->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
