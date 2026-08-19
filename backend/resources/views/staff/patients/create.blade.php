@extends('layouts.staff')

@section('title', 'Register New Patient')
@section('page_title', 'Staff-Assisted Patient Intake & Registration')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('staff.patients.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5">
            &larr; Back to Patient Directory
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
        <div>
            <h2 class="text-base font-bold text-slate-900">New Patient Registration</h2>
            <p class="text-xs text-slate-500 mt-0.5">Register a new patient account directly at the clinic front desk</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4 space-y-1">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('staff.patients.store') }}" class="space-y-6">
            @csrf

            <!-- Account Credentials -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Credentials</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-semibold text-slate-700">Full Name *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="John Doe"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-semibold text-slate-700">Email Address *</label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="patient@example.com"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="phone" class="block text-xs font-semibold text-slate-700">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 555 123 4567"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>

                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Temporary Password *</label>
                        <input type="password" id="password" name="password" required placeholder="Minimum 8 characters"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Health & Emergency Info -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Demographic & Emergency Contact</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label for="blood_type" class="block text-xs font-semibold text-slate-700">Blood Group</label>
                        <select id="blood_type" name="blood_type" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="emergency_contact_name" class="block text-xs font-semibold text-slate-700">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Spouse / Parent Name"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>

                    <div class="space-y-1">
                        <label for="emergency_contact_phone" class="block text-xs font-semibold text-slate-700">Emergency Contact Phone</label>
                        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="+1 555 987 6543"
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('staff.patients.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-cyan-600/20">
                    Register Patient &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
