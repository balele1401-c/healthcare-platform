@extends('layouts.staff')

@section('title', 'Doctor Schedules Coordination')
@section('page_title', 'Doctor Schedules Coordination Matrix')

@section('content')
<div class="space-y-6">
    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Clinic-Wide Doctor Consultation Shifts</h2>
                <p class="text-xs text-slate-500">Monitor doctor availability, shift hours, and weekly consultation capacity</p>
            </div>
        </div>

        <form method="GET" action="{{ route('staff.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <select name="doctor_id" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Medical Doctors</option>
                @foreach ($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>Dr. {{ $doc->user->name ?? 'Doctor' }}</option>
                @endforeach
            </select>

            <select name="day_of_week" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Days of Week</option>
                <option value="1" {{ $dayOfWeek == '1' ? 'selected' : '' }}>Monday</option>
                <option value="2" {{ $dayOfWeek == '2' ? 'selected' : '' }}>Tuesday</option>
                <option value="3" {{ $dayOfWeek == '3' ? 'selected' : '' }}>Wednesday</option>
                <option value="4" {{ $dayOfWeek == '4' ? 'selected' : '' }}>Thursday</option>
                <option value="5" {{ $dayOfWeek == '5' ? 'selected' : '' }}>Friday</option>
                <option value="6" {{ $dayOfWeek == '6' ? 'selected' : '' }}>Saturday</option>
                <option value="7" {{ $dayOfWeek == '7' ? 'selected' : '' }}>Sunday</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                    Filter Shifts
                </button>
                @if ($doctorId || $dayOfWeek)
                    <a href="{{ route('staff.schedules.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Doctor</th>
                        <th class="py-3.5 px-6">Specialty</th>
                        <th class="py-3.5 px-6">Day of Week</th>
                        <th class="py-3.5 px-6">Shift Hours</th>
                        <th class="py-3.5 px-6">Mode</th>
                        <th class="py-3.5 px-6">Duration</th>
                        <th class="py-3.5 px-6">Max Capacity</th>
                        <th class="py-3.5 px-6 text-right">Shift Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                    @endphp
                    @forelse ($schedules as $sch)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900">Dr. {{ $sch->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-slate-400">{{ $sch->doctor->facility ?? 'Main Clinic' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $sch->doctor->specialty->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-900 text-xs">
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
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No consultation shifts found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
