@extends('layouts.staff')

@section('title', 'Patient Intake & Directory')
@section('page_title', 'Patient Intake & Operations Roster')

@section('content')
<div class="space-y-6">
    <!-- Action & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Patient Roster & Directory</h2>
            <p class="text-xs text-slate-500">Search and intake patients for front-desk clinic appointments</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <form method="GET" action="{{ route('staff.patients.index') }}" class="flex-1 sm:w-64">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search patient name, email..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <a href="{{ route('staff.patients.create') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs transition-all shadow-xs flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>+ Register Patient</span>
            </a>
        </div>
    </div>

    <!-- Patients Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Patient ID</th>
                        <th class="py-3.5 px-6">Patient Name</th>
                        <th class="py-3.5 px-6">Contact Info</th>
                        <th class="py-3.5 px-6">Blood Type</th>
                        <th class="py-3.5 px-6">Emergency Contact</th>
                        <th class="py-3.5 px-6">Registered On</th>
                        <th class="py-3.5 px-6 text-right">Operational Profile</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($patients as $pat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                #PAT-{{ $pat->id }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $pat->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">{{ $pat->user->email ?? 'No email' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $pat->user->phone ?? 'Not provided' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono font-semibold">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800">
                                    {{ $pat->blood_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                @if ($pat->emergency_contact_name)
                                    <div class="font-medium text-slate-900">{{ $pat->emergency_contact_name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $pat->emergency_contact_phone ?? 'No phone' }}</div>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ $pat->created_at ? $pat->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('staff.patients.show', $pat->id) }}"
                                   class="inline-flex items-center text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                                    View Profile &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No registered patients found.
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
