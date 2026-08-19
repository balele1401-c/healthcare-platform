<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorAuthController extends Controller
{
    /**
     * Display the Doctor Login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === UserRole::DOCTOR && Auth::user()->doctor) {
                return redirect()->route('doctor.dashboard');
            }
            Auth::logout();
        }

        return view('doctor.auth.login');
    }

    /**
     * Process Doctor Authentication attempt.
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

            if ($user->role !== UserRole::DOCTOR || ! $user->doctor) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'UNAUTHORIZED_DOCTOR_LOGIN_ATTEMPT',
                    'entity_type' => 'Auth',
                    'entity_id' => $user->id,
                    'new_data' => ['attempted_role' => $user->role->value, 'ip' => $request->ip()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return back()->withErrors([
                    'email' => 'Access denied. You must be an authorized Medical Doctor with a clinical profile to sign in.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'DOCTOR_LOGIN_SUCCESS',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'new_data' => ['email' => $user->email, 'doctor_id' => $user->doctor->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('doctor.dashboard'))
                ->with('success', 'Welcome, Dr. ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our medical practitioner records.',
        ])->onlyInput('email');
    }

    /**
     * Log the Doctor out of the portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'DOCTOR_LOGOUT',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('doctor.login')
            ->with('info', 'You have been securely logged out of the Doctor Portal.');
    }
}
