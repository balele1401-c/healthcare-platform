<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffProfileController extends Controller
{
    /**
     * Display staff operational profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $staff = $user->staff;

        return view('staff.profile', compact('user', 'staff'));
    }

    /**
     * Update staff profile details.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $staff = $user->staff;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['required', 'string', 'max:100'],
            'facility' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        $staff->update([
            'department' => $validated['department'],
            'facility' => $validated['facility'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'STAFF_UPDATE_PROFILE',
            'entity_type' => 'Staff',
            'entity_id' => $staff->id,
            'new_data' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Your operational profile details have been updated.');
    }
}
