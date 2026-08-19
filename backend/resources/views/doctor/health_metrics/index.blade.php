@extends('layouts.doctor')

@section('title', 'Patient Health Metrics')
@section('page_title', 'Remote Patient Health Monitoring')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Biometric Patient Readings</h2>
            <p class="text-xs text-slate-500">Continuous remote health tracking feeds from your assigned patients</p>
        </div>

        <form method="GET" action="{{ route('doctor.health-metrics.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Patient name..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="metric_type" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Biomarkers</option>
                <option value="blood_pressure" {{ $metricType === 'blood_pressure' ? 'selected' : '' }}>Blood Pressure</option>
                <option value="heart_rate" {{ $metricType === 'heart_rate' ? 'selected' : '' }}>Heart Rate</option>
                <option value="blood_glucose" {{ $metricType === 'blood_glucose' ? 'selected' : '' }}>Blood Glucose</option>
                <option value="oxygen_saturation" {{ $metricType === 'oxygen_saturation' ? 'selected' : '' }}>Oxygen SpO2</option>
                <option value="temperature" {{ $metricType === 'temperature' ? 'selected' : '' }}>Body Temperature</option>
                <option value="weight" {{ $metricType === 'weight' ? 'selected' : '' }}>Weight</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $metricType)
                <a href="{{ route('doctor.health-metrics.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Health Metrics Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Timestamp</th>
                        <th class="py-3.5 px-6">Patient</th>
                        <th class="py-3.5 px-6">Biomarker</th>
                        <th class="py-3.5 px-6">Reading Value</th>
                        <th class="py-3.5 px-6">Clinical Status</th>
                        <th class="py-3.5 px-6 text-right">Chart</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($metrics as $m)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ date('M d, Y H:i', strtotime($m->measured_at)) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900">{{ $m->patient->user->name ?? 'Patient' }}</div>
                                <div class="text-xs text-slate-400">ID: #{{ $m->patient_id }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 uppercase tracking-wider text-[10px]">
                                    {{ is_object($m->metric_type) ? $m->metric_type->label() : str_replace('_', ' ', $m->metric_type) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900 text-sm">
                                {{ $m->value }} <span class="text-xs text-slate-500 font-sans font-normal">{{ $m->unit }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                    Normal Range
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('doctor.patients.show', $m->patient_id) }}"
                                   class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                                    Patient Chart &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No health metric logs recorded by patients.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($metrics->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $metrics->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
