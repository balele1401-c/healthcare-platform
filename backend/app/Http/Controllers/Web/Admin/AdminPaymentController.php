<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    /**
     * Display financial payments and transaction receipts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Payment::with(['patient.user', 'appointment.doctor.user']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('appointment', fn ($aq) => $aq->where('booking_code', 'like', "%{$search}%"));
            });
        }

        if (! empty($status)) {
            $query->where('payment_status', $status);
        }

        $payments = $query->latest()->paginate(10)->withQueryString();
        $totalPaidAmount = (float) Payment::where('payment_status', 'paid')->sum('amount');
        $totalPendingAmount = (float) Payment::where('payment_status', 'pending')->sum('amount');

        return view('admin.payments.index', compact('payments', 'search', 'status', 'totalPaidAmount', 'totalPendingAmount'));
    }
}
