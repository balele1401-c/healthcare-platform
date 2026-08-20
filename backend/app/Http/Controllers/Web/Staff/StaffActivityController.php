<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffActivityController extends Controller
{
    /**
     * Display operational activity logs.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = AuditLog::with('user');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $activities = $query->latest()->paginate(20)->withQueryString();

        return view('staff.activity.index', compact('activities', 'search'));
    }
}
