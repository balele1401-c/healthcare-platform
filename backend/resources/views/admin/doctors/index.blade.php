@extends('layouts.admin')

@section('title', 'Doctors Management')
@section('page_title', 'Doctors Management')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Medical Practitioners</h2>
            <p class="text-xs text-slate-500">Monitor registered doctors, clinical specialties, and weekly schedules</p>
        </div>

        <form method="GET" action="{{ route('admin.doctors.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-60">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search doctor, hospital..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="specialty_id" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Specialties</option>
                @foreach ($specialties as $spec)
                    <option value="{{ $spec->id }}" {{ (string)$specialtyId === (string)$spec->id ? 'selected' : '' }}>
                        {{ $spec->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Status</option>
                <option value="available" {{ $status === 'available' ? 'selected' : '' }}>Available</option>
                <option value="unavailable" {{ $status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $specialtyId || $status)
                <a href="{{ route('admin.doctors.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Doctors Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Specialty</th>
                        <th class="py-3.5 px-6">Facility / Hospital</th>
                        <th class="py-3.5 px-6">Experience</th>
                        <th class="py-3.5 px-6">Fee</th>
                        <th class="py-3.5 px-6">Rating</th>
                        <th class="py-3.5 px-6">Availability</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($doctors as $doc)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($doc->user->name ?? 'D', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 text-sm">{{ $doc->user->name ?? 'Doctor' }}</div>
                                        <div class="text-xs text-slate-400">{{ $doc->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                                    {{ $doc->specialty->name ?? 'General Practice' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700 font-medium">
                                {{ $doc->facility ?? 'Metropolitan Medical Center' }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">
                                {{ $doc->experience_years ?? 0 }} Years
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-900">
                                ${{ number_format($doc->consultation_fee, 2) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-1 text-xs font-semibold text-amber-600">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span>{{ number_format($doc->rating, 1) }}</span>
                                    <span class="text-slate-400 font-normal">({{ $doc->review_count }})</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $doc->is_available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $doc->is_available ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $doc->is_available ? 'Active' : 'Offline' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.doctors.show', $doc->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Profile &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
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
