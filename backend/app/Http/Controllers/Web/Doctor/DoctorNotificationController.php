<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorNotificationController extends Controller
{
    /**
     * Display a listing of doctor's clinical notifications.
     */
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->latest()
            ->paginate(15);

        $unreadCount = Notification::where(function ($q) {
            $q->where('user_id', Auth::id())->orWhereNull('user_id');
        })->whereNull('read_at')->count();

        return view('doctor.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark all doctor notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        Notification::where(function ($q) {
            $q->where('user_id', Auth::id())->orWhereNull('user_id');
        })->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All clinical notifications marked as read.');
    }
}
