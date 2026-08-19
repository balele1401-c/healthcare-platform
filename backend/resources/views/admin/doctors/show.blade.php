@extends('layouts.admin')

@section('title', 'Doctor Profile — ' . ($doctor->user->name ?? 'Doctor'))
@section('page_title', 'Doctor Credentials & Schedules')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Doctors Directory
        </a>
        <span class="text-xs text-slate-400">Doctor ID: #{{ $doctor->id }}</span>
    </div>

    <!-- Doctor Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-md shadow-indigo-600/30 flex-shrink-0">
                {{ strtoupper(substr($doctor->user->name ?? 'D', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $doctor->user->name ?? 'Doctor Name' }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                    <span class="font-semibold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full">{{ $doctor->specialty->name ?? 'General Practice' }}</span>
                    <span>&bull;</span>
                    <span>{{ $doctor->facility ?? 'Metropolitan Medical Center' }}</span>
                    <span>&bull;</span>
                    <span>{{ $doctor->user->email ?? '' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 border-t sm:border-t-0 sm:border-l border-slate-100 pt-4 sm:pt-0 sm:pl-6 w-full sm:w-auto">
            <div>
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Consultation Fee</p>
                <p class="text-xl font-bold text-slate-900">${{ number_format($doctor->consultation_fee, 2) }}</p>
            </div>
            <div class="border-l border-slate-100 pl-4">
                <p class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Experience</p>
                <p class="text-sm font-semibold text-slate-800">{{ $doctor->experience_years ?? 0 }} Years</p>
            </div>
        </div>
    </div>

    <!-- Doctor Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Biography & Education -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3">Credentials & Bio</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Education</span>
                    <span class="font-semibold text-slate-800">{{ $doctor->education ?? 'Harvard Medical School' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Biography</span>
                    <p class="text-slate-600 mt-1 leading-relaxed">{{ $doctor->biography ?? 'Clinical medical specialist with board certification.' }}</p>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Contact Phone</span>
                    <span class="font-semibold text-slate-800">{{ $doctor->user->phone ?? 'Not provided' }}</span>
                </div>
            </div>
        </div>

        <!-- Weekly Consultation Schedules -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="font-semibold text-slate-900 text-sm border-b border-slate-100 pb-3">Weekly Consultation Schedules</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-semibold border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-4">Day of Week</th>
                            <th class="py-2.5 px-4">Working Hours</th>
                            <th class="py-2.5 px-4">Slot Duration</th>
                            <th class="py-2.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $daysMap = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                        @endphp
                        @forelse ($doctor->schedules as $sched)
                            <tr>
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $daysMap[$sched->day_of_week] ?? 'Day ' . $sched->day_of_week }}
                                </td>
                                <td class="py-3 px-4 text-slate-700">
                                    {{ substr($sched->start_time, 0, 5) }} — {{ substr($sched->end_time, 0, 5) }}
                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $sched->slot_duration_minutes ?? 30 }} mins / patient
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $sched->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $sched->is_active ? 'Active' : 'Paused' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">No recurring consultation schedules configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
