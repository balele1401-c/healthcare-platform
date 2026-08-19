<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorProfileController extends Controller
{
    /**
     * Display doctor practitioner profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $doctor = $user->doctor->load('specialty');

        return view('doctor.profile', compact('user', 'doctor'));
    }

    /**
     * Update doctor practitioner profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'education' => ['nullable', 'string', 'max:1000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:70'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'facility' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        $doctor->update([
            'biography' => $validated['biography'] ?? null,
            'education' => $validated['education'] ?? null,
            'experience_years' => $validated['experience_years'],
            'consultation_fee' => $validated['consultation_fee'],
            'facility' => $validated['facility'] ?? null,
            'status' => $validated['status'],
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'DOCTOR_UPDATE_PROFILE',
            'entity_type' => 'Doctor',
            'entity_id' => $doctor->id,
            'new_data' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Your clinical profile has been updated successfully.');
    }
}
