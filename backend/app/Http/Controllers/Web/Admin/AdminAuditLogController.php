<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    /**
     * Display compliance and system audit logs.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $action = $request->query('action');

        $query = AuditLog::with('user');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'ilike', "%{$search}%")
                    ->orWhere('entity_type', 'ilike', "%{$search}%")
                    ->orWhere('ip_address', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if (! empty($action)) {
            $query->where('action', $action);
        }

        $auditLogs = $query->latest()->paginate(15)->withQueryString();
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit_logs.index', compact('auditLogs', 'search', 'action', 'actions'));
    }
}
