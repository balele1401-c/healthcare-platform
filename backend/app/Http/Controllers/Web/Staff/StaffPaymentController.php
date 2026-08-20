<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffPaymentController extends Controller
{
    /**
     * Display a listing of billing payments and settlement statuses.
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
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $totalPaidAmount = Payment::where('status', \App\Enums\PaymentStatus::PAID)->sum('amount');
        $totalPendingAmount = Payment::where('status', \App\Enums\PaymentStatus::PENDING)->sum('amount');

        return view('staff.payments.index', compact('payments', 'search', 'status', 'totalPaidAmount', 'totalPendingAmount'));
    }
}
