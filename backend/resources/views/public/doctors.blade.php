@extends('layouts.public')

@section('meta_title', 'Find a Doctor — Certified Medical Practitioners Directory')
@section('meta_description', 'Browse our directory of certified healthcare specialists. Filter by medical specialty, location, experience, and consultation fees.')

@section('content')
<div class="py-12 sm:py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Heading -->
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Doctor Practitioner Directory
            </h1>
            <p class="text-slate-500 text-sm">
                Browse certified physicians and specialists available for in-person clinic visits and online teleconsultations.
            </p>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('public.doctors') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="relative sm:col-span-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search doctor name, hospital, or clinic..."
                           class="w-full pl-10 pr-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex items-center gap-2">
                    <select name="specialty_id" class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-2xl px-3.5 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <option value="">All Medical Specialties</option>
                        @foreach ($specialties as $spec)
                            <option value="{{ $spec->id }}" {{ $specialtyId == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-2xl text-xs shadow-md shadow-cyan-600/20 transition-colors shrink-0">
                        Search
                    </button>
                    @if ($search || $specialtyId)
                        <a href="{{ route('public.doctors') }}" class="px-3 py-3 text-xs text-slate-500 hover:text-slate-800">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Doctors Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($doctors as $doc)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs hover:border-cyan-300 hover:shadow-lg transition-all space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3.5">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-xs shrink-0">
                                    Dr
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">Dr. {{ $doc->user->name ?? 'Specialist' }}</h3>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 mt-1">
                                        {{ $doc->specialty->name ?? 'General Practice' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <dl class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                            <div class="flex justify-between py-1">
                                <dt class="text-slate-400 font-medium">Affiliation</dt>
                                <dd class="font-semibold text-slate-900">{{ $doc->facility ?? 'Main Medical Center' }}</dd>
                            </div>
                            <div class="flex justify-between py-1">
                                <dt class="text-slate-400 font-medium">Clinical Experience</dt>
                                <dd class="font-semibold text-slate-900">{{ $doc->experience_years }} Years</dd>
                            </div>
                            <div class="flex justify-between py-1">
                                <dt class="text-slate-400 font-medium">Consultation Fee</dt>
                                <dd class="font-mono font-bold text-slate-900">${{ number_format($doc->consultation_fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between py-1">
                                <dt class="text-slate-400 font-medium">Patient Reviews</dt>
                                <dd class="font-bold text-amber-500">★ {{ number_format($doc->rating_average, 1) }} ({{ $doc->rating_count }})</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ route('doctor.login') }}"
                           class="w-full block py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-center font-semibold rounded-xl text-xs transition-colors">
                            Book Consultation &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center text-slate-400 text-sm bg-white rounded-3xl border border-slate-200">
                    No medical specialists found matching your search criteria.
                </div>
            @endforelse
        </div>

        @if ($doctors->hasPages())
            <div class="pt-6">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
