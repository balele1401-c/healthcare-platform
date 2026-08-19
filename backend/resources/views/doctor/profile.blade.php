@extends('layouts.doctor')

@section('title', 'Practitioner Profile')
@section('page_title', 'Doctor Credentials & Clinical Profile')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Profile Summary Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-3xl shadow-lg shadow-teal-600/30 shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900">Dr. {{ $user->name }}</h2>
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200 uppercase tracking-wider">
                        {{ $doctor->specialty->name ?? 'Specialist' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $user->email }} &bull; {{ $user->phone ?? 'No phone entered' }}</p>
                <div class="flex items-center gap-3 mt-3 text-xs text-slate-500">
                    <span>License: <strong class="font-mono text-slate-800">{{ $doctor->license_number }}</strong></span>
                    <span>&bull;</span>
                    <span>Rating: <strong class="text-amber-500 font-bold">★ {{ number_format($doctor->rating, 1) }}</strong> ({{ $doctor->review_count }} reviews)</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('doctor.logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 text-xs font-semibold transition-colors">
                Sign Out
            </button>
        </form>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Edit Practitioner Credentials</h3>
            <p class="text-xs text-slate-500">Update practice information, consultation fees, and facility affiliation</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4 space-y-1">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.profile.update') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-700">Full Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-semibold text-slate-700">Phone Contact</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="space-y-1.5">
                    <label for="experience_years" class="block text-xs font-semibold text-slate-700">Years of Experience *</label>
                    <input type="number" id="experience_years" name="experience_years" required min="0" value="{{ old('experience_years', $doctor->experience_years) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="consultation_fee" class="block text-xs font-semibold text-slate-700">Consultation Fee ($) *</label>
                    <input type="number" step="0.01" id="consultation_fee" name="consultation_fee" required min="0" value="{{ old('consultation_fee', $doctor->consultation_fee) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="space-y-1.5">
                    <label for="status" class="block text-xs font-semibold text-slate-700">Practice Status *</label>
                    <select id="status" name="status" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="active" {{ old('status', $doctor->status) === 'active' ? 'selected' : '' }}>Active (Accepting Patients)</option>
                        <option value="inactive" {{ old('status', $doctor->status) === 'inactive' ? 'selected' : '' }}>Inactive / On Leave</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="facility" class="block text-xs font-semibold text-slate-700">Primary Hospital / Clinic Facility</label>
                <input type="text" id="facility" name="facility" value="{{ old('facility', $doctor->facility) }}"
                       placeholder="e.g. Metropolitan Medical Center"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="space-y-1.5">
                <label for="education" class="block text-xs font-semibold text-slate-700">Medical Education & Qualifications</label>
                <input type="text" id="education" name="education" value="{{ old('education', $doctor->education) }}"
                       placeholder="e.g. MD - Harvard Medical School, Residency at Johns Hopkins"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
            </div>

            <div class="space-y-1.5">
                <label for="biography" class="block text-xs font-semibold text-slate-700">Professional Clinical Biography</label>
                <textarea id="biography" name="biography" rows="4"
                          placeholder="Summary of medical expertise and clinical philosophy..."
                          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('biography', $doctor->biography) }}</textarea>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-all shadow-md shadow-teal-600/30">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
