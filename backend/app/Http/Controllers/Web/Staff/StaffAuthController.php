<?php

namespace App\Http\Controllers\Web\Staff;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffAuthController extends Controller
{
    /**
     * Display the Staff Login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === UserRole::STAFF && Auth::user()->staff) {
                return redirect()->route('staff.dashboard');
            }
            Auth::logout();
        }

        return view('staff.auth.login');
    }

    /**
     * Process Staff Authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->role !== UserRole::STAFF || ! $user->staff) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'UNAUTHORIZED_STAFF_LOGIN_ATTEMPT',
                    'entity_type' => 'Auth',
                    'entity_id' => $user->id,
                    'new_data' => ['attempted_role' => $user->role->value, 'ip' => $request->ip()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return back()->withErrors([
                    'email' => 'Access denied. You must be an authorized Clinical Operations Staff member with a valid profile.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'STAFF_LOGIN_SUCCESS',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'new_data' => ['email' => $user->email, 'staff_id' => $user->staff->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('staff.dashboard'))
                ->with('success', 'Welcome, ' . $user->name . ' (Operations Staff)');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our staff records.',
        ])->onlyInput('email');
    }

    /**
     * Log the Staff user out of the portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'STAFF_LOGOUT',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login')
            ->with('info', 'You have been securely logged out of the Operations Portal.');
    }
}
