@extends('layouts.admin')

@section('title', 'Appointments Management')
@section('page_title', 'Appointments & Consultations')

@section('content')
<div class="space-y-6">
    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Consultation Bookings</h2>
            <p class="text-xs text-slate-500">Monitor in-person and teleconsultation bookings across all clinical facilities</p>
        </div>

        <form method="GET" action="{{ route('admin.appointments.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Code, patient, doctor..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <input type="date" name="date" value="{{ $date }}"
                   class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-700">

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $status || $date)
                <a href="{{ route('admin.appointments.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Appointments Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Booking Code</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Doctor / Specialty</th>
                        <th class="py-3.5 px-6">Schedule</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Total Amount</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($appointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-semibold text-slate-900 text-xs">
                                {{ $apt->booking_code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $apt->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">{{ $apt->patient->user->phone ?? $apt->patient->user->email ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">{{ $apt->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-teal-600 font-medium">{{ $apt->doctor->specialty->name ?? 'General' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <div class="font-semibold text-slate-900">{{ date('M d, Y', strtotime($apt->appointment_date)) }}</div>
                                <div class="text-slate-400">{{ substr($apt->appointment_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $apt->consultation_type->value === 'online' ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $apt->consultation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs font-bold text-slate-900">
                                ${{ number_format($apt->total_amount, 2) }}
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
                                <a href="{{ route('admin.appointments.show', $apt->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Details &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No appointments found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
