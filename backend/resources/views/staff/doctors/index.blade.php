@extends('layouts.staff')

@section('title', 'Doctor Directory')
@section('page_title', 'Doctor Directory & Practitioner Roster')

@section('content')
<div class="space-y-6">
    <!-- Header & Multi-Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Doctor Practitioner Directory</h2>
                <p class="text-xs text-slate-500">View medical specialists, department affiliations, and consultation availability</p>
            </div>
        </div>

        <form method="GET" action="{{ route('staff.doctors.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search doctor name or facility..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="specialty_id" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Specialties</option>
                @foreach ($specialties as $spec)
                    <option value="{{ $spec->id }}" {{ $specialtyId == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <select name="status" class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active On-Duty</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Off-Duty</option>
                </select>

                <button type="submit" class="py-2 px-4 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Doctor Directory Cards / Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Medical Specialty</th>
                        <th class="py-3.5 px-6">Affiliation / Facility</th>
                        <th class="py-3.5 px-6">Consultation Fee</th>
                        <th class="py-3.5 px-6">Duty Status</th>
                        <th class="py-3.5 px-6 text-right">Schedule Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($doctors as $doc)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900">Dr. {{ $doc->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $doc->license_number }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $doc->specialty->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $doc->facility ?? 'Main Clinic' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono font-bold text-slate-900">
                                ${{ number_format($doc->consultation_fee, 2) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $doc->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('staff.doctors.show', $doc->id) }}"
                                   class="inline-flex items-center text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                                    View Schedule &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No doctors found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($doctors->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
