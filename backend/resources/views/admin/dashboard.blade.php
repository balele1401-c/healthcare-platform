@extends('layouts.admin')

@section('title', 'Admin Operations Overview')
@section('page_title', 'Operational Overview')

@section('content')
<div class="space-y-6">
    <!-- Top KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Patients -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Patients</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ number_format($totalPatients) }}</h3>
                <div class="flex items-center gap-1 mt-2 text-xs font-medium text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Registered in Platform</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- 2. Total Doctors -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Doctors</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ number_format($totalDoctors) }}</h3>
                <div class="flex items-center gap-1 mt-2 text-xs font-medium text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $activeDoctors }} Available Today</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        <!-- 3. Today's Appointments -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Visits</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ number_format($appointmentsToday) }}</h3>
                <div class="flex items-center gap-1 mt-2 text-xs font-medium text-slate-500">
                    <span>Scheduled on {{ date('M d, Y') }}</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- 4. Pending Appointments -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Bookings</p>
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">{{ number_format($pendingAppointments) }}</h3>
                <div class="flex items-center gap-1 mt-2 text-xs font-medium text-amber-600">
                    <span>{{ $confirmedAppointments }} Confirmed</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Secondary Financial & Prescriptions Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Settled Billing Revenue</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-xs text-slate-500 mt-1">Verified patient payments in platform</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Digital Prescriptions</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($activePrescriptions) }}</h3>
                <p class="text-xs text-slate-500 mt-1">Active medications currently in therapy</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Appointment Distribution Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-slate-900 text-base">Consultation Status Breakdown</h3>
                    <p class="text-xs text-slate-500">Real-time distribution of patient appointments</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">Live PostgreSQL Data</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="appointmentStatusChart"></canvas>
            </div>
        </div>

        <!-- Recent Registered Patients -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-900 text-base">Recent Patients</h3>
                    <a href="{{ route('admin.patients.index') }}" class="text-xs font-medium text-teal-600 hover:text-teal-700">View All</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentPatients as $p)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($p->user->name ?? 'P', 0, 1)) }}
                                </div>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $p->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $p->user->email ?? '' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $p->blood_type ?? 'N/A' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">No patients registered yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <a href="{{ route('admin.patients.index') }}" class="block w-full text-center py-2 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Manage All Patients
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Appointments Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 flex items-center justify-between border-b border-slate-200">
            <div>
                <h3 class="font-semibold text-slate-900 text-base">Recent Consultations</h3>
                <p class="text-xs text-slate-500">Latest scheduled patient visits and clinical appointments</p>
            </div>
            <a href="{{ route('admin.appointments.index') }}" class="text-xs font-medium text-teal-600 hover:text-teal-700">
                View All Appointments &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Booking Code</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Doctor / Specialty</th>
                        <th class="py-3.5 px-6">Schedule</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentAppointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-medium text-slate-900 text-xs">
                                {{ $apt->booking_code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $apt->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">{{ $apt->patient->user->email ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $apt->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-teal-600 font-medium">{{ $apt->doctor->specialty->name ?? 'General' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="text-slate-900">{{ date('M d, Y', strtotime($apt->appointment_date)) }}</div>
                                <div class="text-xs text-slate-400">{{ substr($apt->appointment_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $apt->consultation_type->value === 'online' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $apt->consultation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @php
                                    $badgeStyles = match($apt->status->value) {
                                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeStyles }}">
                                    {{ $apt->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('admin.appointments.show', $apt->id) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                No appointments recorded in the database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Audit Activity Feed -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-slate-900 text-base">System Audit Activity</h3>
                <p class="text-xs text-slate-500">Security compliance, authentication events, and administrative changes</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-medium text-teal-600 hover:text-teal-700">
                View Full Audit Trail &rarr;
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($recentAuditLogs as $log)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start justify-between gap-4 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-md bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-[10px] mt-0.5">
                            {{ substr($log->action, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">
                                <span class="font-mono text-teal-700">{{ $log->action }}</span>
                                by <span class="font-semibold">{{ $log->user->name ?? 'System' }}</span>
                            </p>
                            <p class="text-slate-500 text-[11px] mt-0.5">
                                Entity: {{ $log->entity_type }} (ID: {{ $log->entity_id ?? 'N/A' }}) &bull; IP: {{ $log->ip_address }}
                            </p>
                        </div>
                    </div>
                    <span class="text-slate-400 whitespace-nowrap text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">No audit activity logged.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('appointmentStatusChart');
        if (ctx && window.Chart) {
            const stats = @json($appointmentStats);
            new window.Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                    datasets: [{
                        label: 'Appointments',
                        data: [stats.pending, stats.confirmed, stats.completed, stats.cancelled],
                        backgroundColor: ['#f59e0b', '#10b981', '#64748b', '#f43f5e'],
                        borderRadius: 8,
                        barThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
