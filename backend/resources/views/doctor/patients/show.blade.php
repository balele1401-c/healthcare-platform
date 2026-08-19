@extends('layouts.doctor')

@section('title', 'Patient Chart — ' . ($patient->user->name ?? 'Patient'))
@section('page_title', 'Comprehensive Patient Clinical Chart')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('doctor.patients.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Patient Roster
        </a>
        <span class="font-mono text-xs text-slate-400 font-semibold">Patient ID: #PAT-{{ $patient->id }}</span>
    </div>

    <!-- Patient Demographic Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-2xl shadow-md">
                {{ strtoupper(substr($patient->user->name ?? 'P', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-slate-900">{{ $patient->user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Blood: {{ $patient->blood_type ?? 'N/A' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ $patient->user->email }} &bull; {{ $patient->user->phone ?? 'No phone' }}</p>
                <div class="flex items-center gap-4 mt-2 text-xs text-slate-600">
                    <span>Height: <strong class="text-slate-800">{{ $patient->height ? $patient->height . ' cm' : 'N/A' }}</strong></span>
                    <span>Weight: <strong class="text-slate-800">{{ $patient->weight ? $patient->weight . ' kg' : 'N/A' }}</strong></span>
                    <span>Allergies: <strong class="text-rose-600">{{ $patient->allergies ?? 'None recorded' }}</strong></span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('doctor.medical-records.create', ['patient_id' => $patient->id]) }}"
               class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs">
                + Document Visit
            </a>
            <a href="{{ route('doctor.prescriptions.create', ['patient_id' => $patient->id]) }}"
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                + New Rx
            </a>
        </div>
    </div>

    <!-- Medical Records & Prescriptions Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Medical Records History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Documented Clinical Records</h3>
                <span class="text-xs font-semibold text-slate-400">{{ $patient->medicalRecords->count() }} Records</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($patient->medicalRecords as $rec)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div>
                            <span class="font-mono text-[11px] font-bold text-slate-900">{{ $rec->record_number }}</span>
                            <div class="text-xs font-semibold text-slate-800 mt-0.5">{{ $rec->diagnosis }}</div>
                            <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $rec->chief_complaint }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 font-mono block">{{ date('M d, Y', strtotime($rec->visit_date)) }}</span>
                            <a href="{{ route('doctor.medical-records.show', $rec->id) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Open &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No clinical records documented yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Prescriptions Issued -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Issued Prescriptions</h3>
                <span class="text-xs font-semibold text-slate-400">{{ $patient->prescriptions->count() }} Prescriptions</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($patient->prescriptions as $rx)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[11px] font-bold text-slate-900">{{ $rx->prescription_code }}</span>
                                <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold {{ $rx->status->value === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $rx->status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-600 mt-1 font-medium">{{ $rx->items->count() }} Medications prescribed</div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 font-mono block">{{ date('M d, Y', strtotime($rx->prescription_date)) }}</span>
                            <a href="{{ route('doctor.prescriptions.show', $rx->id) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Details &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No electronic prescriptions issued yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Health Metrics & Past Appointments Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Health Metrics -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-sm">Self-Reported Health Metrics</h3>
            </div>
            <div class="p-4">
                @if ($patient->healthMetrics->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($patient->healthMetrics as $metric)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ is_object($metric->metric_type) ? $metric->metric_type->label() : str_replace('_', ' ', $metric->metric_type) }}</span>
                                <div class="text-base font-bold text-slate-900 mt-0.5">{{ $metric->value }} <span class="text-xs text-slate-500 font-normal">{{ $metric->unit }}</span></div>
                                <span class="text-[10px] text-slate-400 block mt-1">{{ date('M d, H:i', strtotime($metric->measured_at)) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-6">No vital signs or health metrics logged by patient.</p>
                @endif
            </div>
        </div>

        <!-- Appointment History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-sm">Consultation History</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($patient->appointments as $apt)
                    <div class="p-4 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                        <div>
                            <span class="font-mono font-bold text-slate-900">{{ $apt->booking_code }}</span>
                            <span class="text-slate-500 block text-[11px]">{{ date('M d, Y', strtotime($apt->appointment_date)) }} at {{ substr($apt->appointment_time, 0, 5) }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full font-semibold {{ match($apt->status->value) {
                            'confirmed' => 'bg-emerald-50 text-emerald-700',
                            'completed' => 'bg-slate-100 text-slate-700',
                            'cancelled' => 'bg-rose-50 text-rose-700',
                            default => 'bg-amber-50 text-amber-700',
                        } }}">
                            {{ $apt->status->label() }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">No appointment history.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
