<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Display the Admin Login view.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === UserRole::ADMIN) {
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
        }

        return view('admin.auth.login');
    }

    /**
     * Process Admin Authentication attempt.
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

            if ($user->role !== UserRole::ADMIN) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'UNAUTHORIZED_ADMIN_LOGIN_ATTEMPT',
                    'entity_type' => 'Auth',
                    'entity_id' => $user->id,
                    'new_data' => ['attempted_role' => $user->role->value, 'ip' => $request->ip()],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return back()->withErrors([
                    'email' => 'Access denied. You must have Administrator privileges to log in here.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'ADMIN_LOGIN_SUCCESS',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'new_data' => ['email' => $user->email],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our administrator records.',
        ])->onlyInput('email');
    }

    /**
     * Log the Administrator out of the platform.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'ADMIN_LOGOUT',
                'entity_type' => 'Auth',
                'entity_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('info', 'You have been securely logged out.');
    }
}
