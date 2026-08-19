@extends('layouts.staff')

@section('title', 'Patient #' . $patient->id . ' Profile')
@section('page_title', 'Patient Demographic Profile — ' . ($patient->user->name ?? 'Patient'))

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- Back Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.patients.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Back to Patient Roster
        </a>
    </div>

    <!-- 2 Column Operational Demographic Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Patient Summary Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3.5">
                <div class="w-14 h-14 rounded-2xl bg-cyan-600 text-white flex items-center justify-center font-bold text-xl shadow-xs">
                    {{ strtoupper(substr($patient->user->name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">{{ $patient->user->name ?? 'Patient' }}</h3>
                    <p class="text-xs text-slate-400 font-mono">ID: #PAT-{{ $patient->id }}</p>
                </div>
            </div>

            <dl class="space-y-2.5 text-xs pt-2 border-t border-slate-100">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-400 font-medium">Email</dt>
                    <dd class="font-semibold text-slate-900">{{ $patient->user->email ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-400 font-medium">Phone</dt>
                    <dd class="font-semibold text-slate-900">{{ $patient->user->phone ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-400 font-medium">Blood Group</dt>
                    <dd class="font-bold text-slate-900 font-mono">{{ $patient->blood_type ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-400 font-medium">Emergency Contact</dt>
                    <dd class="font-semibold text-slate-900">{{ $patient->emergency_contact_name ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-400 font-medium">Emergency Phone</dt>
                    <dd class="font-semibold text-slate-900">{{ $patient->emergency_contact_phone ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-400 font-medium">Registered On</dt>
                    <dd class="font-semibold text-slate-900">{{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Right Side: Clinical Confidentiality Notice & Appointments -->
        <div class="md:col-span-2 space-y-6">
            <!-- Clinical Confidentiality Banner (Medical Record & Prescription Restriction) -->
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 space-y-2">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Clinical Access Restricted — Physician Only</span>
                </div>
                <p class="text-[11px] text-amber-800 leading-relaxed">
                    Electronic Medical Records (EMR), clinical diagnoses, physician progress notes, and electronic prescriptions are strictly confidential clinical assets accessible only by licensed medical practitioners and authorized clinical physicians.
                </p>
            </div>

            <!-- Appointment History Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Consultation Booking History</h3>
                    <span class="text-xs text-slate-400">{{ $patient->appointments->count() }} Total Encounters</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($patient->appointments as $apt)
                        <div class="p-4 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                            <div class="space-y-0.5">
                                <span class="font-mono font-bold text-slate-900">{{ $apt->booking_code }}</span>
                                <div class="text-slate-500">Dr. {{ $apt->doctor->user->name ?? 'Doctor' }} &bull; {{ $apt->doctor->specialty->name ?? 'Specialist' }}</div>
                                <div class="text-slate-400 text-[11px]">{{ date('M d, Y', strtotime($apt->appointment_date)) }} at {{ substr($apt->appointment_time, 0, 5) }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ match($apt->status->value) {
                                    'confirmed' => 'bg-emerald-50 text-emerald-700',
                                    'in_consultation' => 'bg-cyan-50 text-cyan-700',
                                    'completed' => 'bg-slate-100 text-slate-700',
                                    'cancelled' => 'bg-rose-50 text-rose-700',
                                    default => 'bg-amber-50 text-amber-700',
                                } }}">
                                    {{ $apt->status->label() }}
                                </span>
                                <a href="{{ route('staff.appointments.show', $apt->id) }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                                    Details &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No appointment records found for this patient.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
