@extends('layouts.admin')

@section('title', 'Medical Specialties Overview')
@section('page_title', 'Medical Specialties Directory')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Clinical Specialties</h2>
            <p class="text-xs text-slate-500">Medical disciplines and specialist doctor distribution</p>
        </div>
        <span class="px-3 py-1 bg-teal-50 text-teal-700 text-xs font-semibold rounded-full border border-teal-200">
            {{ $specialties->count() }} Specialties Active
        </span>
    </div>

    <!-- Specialties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($specialties as $spec)
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-lg">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            {{ $spec->doctors_count }} {{ Str::plural('Doctor', $spec->doctors_count) }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $spec->name }}</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                        {{ $spec->description ?? 'Comprehensive clinical diagnostics, preventive healthcare, and specialist treatment.' }}
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-mono">Slug: {{ $spec->slug }}</span>
                    <a href="{{ route('admin.doctors.index', ['specialty_id' => $spec->id]) }}" class="font-semibold text-teal-600 hover:text-teal-700">
                        View Doctors &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-slate-400 text-xs">
                No specialties configured in the platform.
            </div>
        @endforelse
    </div>
</div>
@endsection
