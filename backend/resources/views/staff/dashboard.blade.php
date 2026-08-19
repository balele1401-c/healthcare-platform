@extends('layouts.staff')

@section('title', 'Clinic Operations Dashboard')
@section('page_title', 'Clinic Front-Desk Operations Cockpit')

@section('content')
<div class="space-y-8">
    <!-- Top Welcome Card -->
    <div class="bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border border-slate-800">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-semibold border border-cyan-500/30">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span>Front Desk Operations Active</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Operations Cockpit — {{ Auth::user()->name }}</h2>
            <p class="text-xs text-cyan-200/80">Department: {{ $staff->department ?? 'General Operations' }} &bull; {{ $staff->facility ?? 'Metropolitan Medical Center' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('staff.patients.create') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                + Register Patient
            </a>
            <a href="{{ route('staff.appointments.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs transition-all border border-white/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Coordinate Appointments
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
                <span class="text-xs font-semibold text-cyan-600">Today</span>
            </div>
        </div>

        <!-- Pending Confirmation -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pending Bookings</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-amber-600">{{ $pendingAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-amber-600">Action</span>
            </div>
        </div>

        <!-- Confirmed -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Confirmed</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-emerald-600">{{ $confirmedAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-emerald-600">In-Shift</span>
            </div>
        </div>

        <!-- Active Doctors -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Doctors</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ $activeDoctorsCount }}</span>
                <span class="text-xs font-semibold text-indigo-600">On-Duty</span>
            </div>
        </div>

        <!-- Registered Patients -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Patients</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-slate-900">{{ $registeredPatientsCount }}</span>
                <span class="text-xs font-semibold text-slate-400">Roster</span>
            </div>
        </div>

        <!-- Unpaid Invoices -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Unpaid Invoices</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-rose-600">{{ $unpaidAppointmentsCount }}</span>
                <span class="text-xs font-semibold text-rose-600">Pending</span>
            </div>
        </div>
    </div>

    <!-- Operational Appointments Coordination Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Recent Booking Coordination Queue</h3>
                <p class="text-xs text-slate-500">Live operational appointment queue for check-in and patient intake</p>
            </div>
            <a href="{{ route('staff.appointments.index') }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                View All Appointments &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Booking Code</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Assigned Doctor</th>
                        <th class="py-3.5 px-6">Date & Time</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Coordination Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentAppointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ $apt->booking_code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $apt->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #PAT-{{ $apt->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">Dr. {{ $apt->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-cyan-600 font-semibold">{{ $apt->doctor->specialty->name ?? 'General' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900 font-medium">
                                <div>{{ date('M d, Y', strtotime($apt->appointment_date)) }}</div>
                                <div class="text-slate-400 font-mono text-[11px]">{{ substr($apt->appointment_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ match($apt->status->value) {
                                    'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'in_consultation' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                    'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                                } }}">
                                    {{ $apt->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('staff.appointments.show', $apt->id) }}"
                                   class="inline-flex items-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                    Coordinate &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No operational appointments in queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Section: Operational Activity Log Stream -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Live Operational Activity Trail</h3>
                <p class="text-xs text-slate-500">System actions, patient registrations, and appointment modifications</p>
            </div>
            <a href="{{ route('staff.activity.index') }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                View Full Trail &rarr;
            </a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($recentActivities as $act)
                <div class="p-4 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                        <div>
                            <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[11px]">{{ $act->action }}</span>
                            <span class="text-slate-600 ml-2">by <strong class="text-slate-900">{{ $act->user->name ?? 'System' }}</strong></span>
                        </div>
                    </div>
                    <span class="text-slate-400 font-mono text-[11px]">{{ $act->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">No activity logs recorded.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
