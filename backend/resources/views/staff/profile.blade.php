@extends('layouts.staff')

@section('title', 'Staff Profile')
@section('page_title', 'Staff Operational Profile & Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
            <div class="w-16 h-16 rounded-2xl bg-cyan-600 text-white flex items-center justify-center font-bold text-2xl shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-cyan-600 font-semibold uppercase tracking-wider">{{ $staff->department ?? 'Operations' }} &bull; Staff ID: {{ $staff->employee_number ?? '#EMP-001' }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 mt-1">
                    Front Desk Active
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4 space-y-1">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('staff.profile.update') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-semibold text-slate-700">Full Name</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                </div>

                <div class="space-y-1">
                    <label for="email" class="block text-xs font-semibold text-slate-700">Email Address (Read-Only)</label>
                    <input type="email" id="email" disabled value="{{ $user->email }}"
                           class="w-full text-xs bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-semibold text-slate-700">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                </div>

                <div class="space-y-1">
                    <label for="department" class="block text-xs font-semibold text-slate-700">Department</label>
                    <input type="text" id="department" name="department" required value="{{ old('department', $staff->department) }}"
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                </div>
            </div>

            <div class="space-y-1">
                <label for="facility" class="block text-xs font-semibold text-slate-700">Assigned Medical Facility</label>
                <input type="text" id="facility" name="facility" value="{{ old('facility', $staff->facility) }}" placeholder="Main Clinic / Center"
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
            </div>

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-cyan-600/20">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
