<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Display administrator profile.
     */
    public function index(): View
    {
        $user = Auth::user();
        $recentLogins = AuditLog::where('user_id', $user->id)
            ->whereIn('action', ['ADMIN_LOGIN_SUCCESS', 'SYSTEM_INIT'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.profile', compact('user', 'recentLogins'));
    }
}
