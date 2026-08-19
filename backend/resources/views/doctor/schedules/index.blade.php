@extends('layouts.doctor')

@section('title', 'Consultation Schedule Matrix')
@section('page_title', 'Clinical Practice Schedules & Slot Management')

@section('content')
<div class="space-y-8 max-w-6xl">
    <!-- Add New Schedule Slot Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Configure Weekly Consultation Shift</h3>
            <p class="text-xs text-slate-500">Define recurring weekly consultation slots available for patient booking</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-3 space-y-1">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.schedules.store') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Day of Week *</label>
                <select name="day_of_week" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                    <option value="6">Saturday</option>
                    <option value="7">Sunday</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Start Time *</label>
                <input type="text" name="start_time" required placeholder="09:00" value="09:00"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">End Time *</label>
                <input type="text" name="end_time" required placeholder="12:00" value="12:00"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Type *</label>
                <select name="consultation_type" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                    <option value="in_person">In-Person Clinic</option>
                    <option value="online">Teleconsultation (Online)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700">Slot (Mins) *</label>
                <input type="number" name="slot_duration_minutes" required min="10" max="120" value="30"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-colors">
                    + Add Slot
                </button>
            </div>
        </form>
    </div>

    <!-- Weekly Matrix Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Configured Consultation Slots</h3>
            <span class="text-xs text-slate-500">{{ $schedules->count() }} active shifts</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Day of Week</th>
                        <th class="py-3 px-6">Time Window</th>
                        <th class="py-3 px-6">Consultation Mode</th>
                        <th class="py-3 px-6">Slot Duration</th>
                        <th class="py-3 px-6">Facility</th>
                        <th class="py-3 px-6">Availability Status</th>
                        <th class="py-3 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                    @endphp
                    @forelse ($schedules as $sch)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-900 text-xs">
                                {{ $days[$sch->day_of_week] ?? 'Day ' . $sch->day_of_week }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-900 font-semibold">
                                {{ substr($sch->start_time, 0, 5) }} — {{ substr($sch->end_time, 0, 5) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $sch->consultation_type->value === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $sch->consultation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700">
                                {{ $sch->slot_duration_minutes }} mins / patient
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $sch->facility }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sch->is_available ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $sch->is_available ? 'Active (Open)' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <form method="POST" action="{{ route('doctor.schedules.toggle', $sch->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-teal-600 hover:text-teal-800">
                                        {{ $sch->is_available ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                No consultation shifts configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
