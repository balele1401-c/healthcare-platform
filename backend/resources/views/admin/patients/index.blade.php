@extends('layouts.admin')

@section('title', 'Patients Management')
@section('page_title', 'Patients Management')

@section('content')
<div class="space-y-6">
    <!-- Header & Search Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Registered Patients</h2>
            <p class="text-xs text-slate-500">Search and monitor patient demographic and biometric profiles</p>
        </div>

        <form method="GET" action="{{ route('admin.patients.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, phone..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="blood_type" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Blood Types</option>
                <option value="A+" {{ $bloodType === 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ $bloodType === 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ $bloodType === 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ $bloodType === 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ $bloodType === 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ $bloodType === 'AB-' ? 'selected' : '' }}>AB-</option>
                <option value="O+" {{ $bloodType === 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ $bloodType === 'O-' ? 'selected' : '' }}>O-</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $bloodType)
                <a href="{{ route('admin.patients.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
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
                        <th class="py-3.5 px-6">Contact Email / Phone</th>
                        <th class="py-3.5 px-6">Blood Type</th>
                        <th class="py-3.5 px-6">Date of Birth</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Registered</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($patients as $patient)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($patient->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 text-sm">{{ $patient->user->name ?? 'Patient' }}</div>
                                        <div class="text-xs text-slate-400">ID: #{{ $patient->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="text-slate-900 text-xs">{{ $patient->user->email ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-400">{{ $patient->user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $patient->blood_type ?? 'Unspecified' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $patient->date_of_birth ? date('M d, Y', strtotime($patient->date_of_birth)) : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Active
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $patient->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.patients.show', $patient->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Profile &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No patients found matching the search criteria.
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
