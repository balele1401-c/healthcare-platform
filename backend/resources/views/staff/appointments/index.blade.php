@extends('layouts.staff')

@section('title', 'Appointment Coordination')
@section('page_title', 'Appointment Management & Coordination')

@section('content')
<div class="space-y-6">
    <!-- Header & Multi-Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Clinic Appointment Coordination</h2>
                <p class="text-xs text-slate-500">Monitor and coordinate booking queues, patient arrivals, and doctor schedules</p>
            </div>
        </div>

        <form method="GET" action="{{ route('staff.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Code, patient, doctor..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Statuses</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="in_consultation" {{ $status === 'in_consultation' ? 'selected' : '' }}>In Consultation</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="specialty_id" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">All Medical Specialties</option>
                @foreach ($specialties as $spec)
                    <option value="{{ $spec->id }}" {{ $specialtyId == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date" value="{{ $date }}"
                   class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500">

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold transition-colors shadow-xs">
                    Filter Queue
                </button>
                @if ($search || $status || $date || $specialtyId)
                    <a href="{{ route('staff.appointments.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
                @endif
            </div>
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
                        <th class="py-3.5 px-6">Doctor & Department</th>
                        <th class="py-3.5 px-6">Date & Time</th>
                        <th class="py-3.5 px-6">Mode</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Billing</th>
                        <th class="py-3.5 px-6 text-right">Coordination</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($appointments as $apt)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-xs">
                                {{ $apt->booking_code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $apt->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $apt->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-medium text-slate-900">Dr. {{ $apt->doctor->user->name ?? 'Doctor' }}</div>
                                <div class="text-xs text-cyan-600 font-semibold">{{ $apt->doctor->specialty->name ?? 'General' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-900 font-medium">
                                <div>{{ date('M d, Y', strtotime($apt->appointment_date)) }}</div>
                                <div class="text-slate-400 font-mono text-[11px]">{{ substr($apt->appointment_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $apt->consultation_type->value === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $apt->consultation_type->label() }}
                                </span>
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
                            <td class="py-3.5 px-6 text-xs">
                                @if ($apt->payment)
                                    <span class="font-bold text-slate-900">${{ number_format($apt->payment->amount, 2) }}</span>
                                    <span class="block text-[10px] text-emerald-600 font-semibold uppercase">{{ is_object($apt->payment->payment_status) ? $apt->payment->payment_status->label() : $apt->payment->payment_status }}</span>
                                @else
                                    <span class="text-slate-400 text-xs font-mono">Unbilled</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('staff.appointments.show', $apt->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                                    Coordinate &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                No appointments found matching the selected filters.
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
