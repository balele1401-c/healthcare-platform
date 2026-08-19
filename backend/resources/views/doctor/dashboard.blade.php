@extends('layouts.doctor')

@section('title', 'Practitioner Dashboard')
@section('page_title', 'Doctor Consultation Cockpit')

@section('content')
<div class="space-y-8">
    <!-- Top Welcome Card -->
    <div class="bg-gradient-to-r from-teal-800 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-semibold border border-teal-500/30">
                <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                <span>Active Consultation Shift</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Good Day, Dr. {{ Auth::user()->name }}</h2>
            <p class="text-xs text-teal-200/80">Department of {{ $doctor->specialty->name ?? 'General Medicine' }} &bull; {{ $doctor->facility ?? 'Metropolitan Medical Center' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('doctor.medical-records.create') }}" class="px-4 py-2.5 bg-teal-500 hover:bg-teal-400 text-white font-semibold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Document Record
            </a>
            <a href="{{ route('doctor.prescriptions.create') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs transition-all border border-white/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Issue Prescription
            </a>
        </div>
    </div>

    <!-- 6 KPI Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Today's Visits -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Today's Visits</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ $todayAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-teal-600">Today</span>
            </div>
        </div>

        <!-- Upcoming -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Upcoming</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ $upcomingAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-indigo-600">Booked</span>
            </div>
        </div>

        <!-- Pending Confirmation -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pending</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-amber-600">{{ $pendingAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-amber-600">Action</span>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Completed</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-emerald-600">{{ $completedConsultationsCount }}</span>
                <span class="text-xs font-semibold text-emerald-600">Done</span>
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Patients</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ $totalPatientsCount }}</span>
                <span class="text-xs font-semibold text-slate-400">Treated</span>
            </div>
        </div>

        <!-- Active Prescriptions -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Rx</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-teal-600">{{ $activePrescriptionsCount }}</span>
                <span class="text-xs font-semibold text-teal-600">Issued</span>
            </div>
        </div>
    </div>

    <!-- Today's Consultation Schedule -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Today's Consultation Schedule</h3>
                <p class="text-xs text-slate-500">{{ date('l, F d, Y') }}</p>
            </div>
            <a href="{{ route('doctor.appointments.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                View All Appointments &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Time Slot</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Clinical Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($todayAppointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ substr($apt->appointment_time, 0, 5) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $apt->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">#{{ $apt->booking_code }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="px-2 py-0.5 rounded-full font-semibold {{ $apt->consultation_type->value === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $apt->consultation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ match($apt->status->value) {
                                    'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'in_consultation' => 'bg-teal-50 text-teal-700 border-teal-200 animate-pulse',
                                    'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                                } }}">
                                    {{ $apt->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right space-x-2">
                                <a href="{{ route('doctor.appointments.show', $apt->id) }}"
                                   class="inline-flex items-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors">
                                    Open Chart
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                No consultations scheduled for today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Two Columns: Recent Clinical Records & Teleconsultation Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Clinical Records -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Recent Clinical Notes</h3>
                <a href="{{ route('doctor.medical-records.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                    View All &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentRecords as $rec)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div>
                            <div class="font-semibold text-slate-900 text-xs">{{ $rec->patient->user->name ?? 'Patient' }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $rec->diagnosis }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 font-mono block">{{ date('M d, Y', strtotime($rec->visit_date)) }}</span>
                            <a href="{{ route('doctor.medical-records.show', $rec->id) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Review &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No recent records documented.</div>
                @endforelse
            </div>
        </div>

        <!-- Teleconsultation Feed -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Teleconsultation Channels</h3>
                <a href="{{ route('doctor.chat.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                    Open Chat &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($conversations as $conv)
                    <a href="{{ route('doctor.chat.show', $conv->id) }}" class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors block">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($conv->patient->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900 text-xs">{{ $conv->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $conv->messages->first()?->message ?? 'No messages yet' }}</div>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $conv->last_message_at ? date('H:i', strtotime($conv->last_message_at)) : '' }}</span>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No active consultation chats.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
