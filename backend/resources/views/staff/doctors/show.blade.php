@extends('layouts.staff')

@section('title', 'Dr. ' . ($doctor->user->name ?? 'Doctor') . ' Schedule')
@section('page_title', 'Doctor Schedule & Profile — Dr. ' . ($doctor->user->name ?? 'Doctor'))

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.doctors.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Back to Doctor Directory
        </a>
    </div>

    <!-- Doctor Profile Summary -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-xs">
                    Dr
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Dr. {{ $doctor->user->name ?? 'Doctor' }}</h3>
                    <p class="text-xs text-indigo-600 font-semibold">{{ $doctor->specialty->name ?? 'General Specialist' }} &bull; {{ $doctor->facility ?? 'Main Clinic' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $doctor->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ ucfirst($doctor->status) }}
                </span>
            </div>
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-4 border-t border-slate-100">
            <div>
                <dt class="text-slate-400 font-medium">License Number</dt>
                <dd class="font-mono font-bold text-slate-900 mt-0.5">{{ $doctor->license_number }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium">Experience</dt>
                <dd class="font-bold text-slate-900 mt-0.5">{{ $doctor->experience_years }} Years</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium">Consultation Fee</dt>
                <dd class="font-mono font-bold text-slate-900 mt-0.5">${{ number_format($doctor->consultation_fee, 2) }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium">Patient Ratings</dt>
                <dd class="font-bold text-amber-500 mt-0.5">★ {{ number_format($doctor->rating_average, 1) }} ({{ $doctor->rating_count }} reviews)</dd>
            </div>
        </dl>
    </div>

    <!-- Weekly Consultation Shift Matrix -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm">Weekly Consultation Shift Slots</h3>
            <p class="text-xs text-slate-500">Active clinic shifts configured for this practitioner</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Day of Week</th>
                        <th class="py-3.5 px-6">Consultation Hours</th>
                        <th class="py-3.5 px-6">Consultation Mode</th>
                        <th class="py-3.5 px-6">Slot Duration</th>
                        <th class="py-3.5 px-6">Max Capacity</th>
                        <th class="py-3.5 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($doctor->schedules as $sch)
                        @php
                            $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-900 text-xs">
                                {{ $days[$sch->day_of_week] ?? 'Day ' . $sch->day_of_week }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono font-medium text-slate-800">
                                {{ substr($sch->start_time, 0, 5) }} &ndash; {{ substr($sch->end_time, 0, 5) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $sch->consultation_type->value === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $sch->consultation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $sch->slot_duration_minutes }} mins
                            </td>
                            <td class="py-3.5 px-6 text-xs font-semibold text-slate-800">
                                {{ $sch->max_patients }} patients
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sch->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $sch->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No consultation shifts configured for this practitioner.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
