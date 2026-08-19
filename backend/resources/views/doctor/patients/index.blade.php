@extends('layouts.doctor')

@section('title', 'My Assigned Patients')
@section('page_title', 'Patient Roster & Medical Charts')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Assigned Patient Roster</h2>
            <p class="text-xs text-slate-500">Patients who have scheduled clinical encounters or consultations with you</p>
        </div>

        <form method="GET" action="{{ route('doctor.patients.index') }}" class="flex items-center gap-2">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient name, email, phone..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold transition-colors shadow-xs">
                Search
            </button>
            @if ($search)
                <a href="{{ route('doctor.patients.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Patients Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Patient Name</th>
                        <th class="py-3.5 px-6">Contact Details</th>
                        <th class="py-3.5 px-6">Blood Type</th>
                        <th class="py-3.5 px-6">Medical History</th>
                        <th class="py-3.5 px-6 text-right">Clinical Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($patients as $pat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900">{{ $pat->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">Record #PAT-{{ $pat->id }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <div class="text-slate-900 font-medium">{{ $pat->user->email }}</div>
                                <div class="text-slate-500">{{ $pat->user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $pat->blood_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 max-w-xs truncate">
                                {{ $pat->medical_history ?? 'None recorded' }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('doctor.patients.show', $pat->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Clinical Chart &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                No assigned patients found under your practice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($patients->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
