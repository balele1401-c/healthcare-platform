<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    /**
     * Display a paginated listing of appointments.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $date = $request->query('date');

        $query = Appointment::with(['patient.user', 'doctor.user', 'doctor.specialty', 'payment']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'ilike', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($dq) => $dq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($date)) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->latest('appointment_date')->latest('appointment_time')->paginate(10)->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'search', 'status', 'date'));
    }

    /**
     * Display appointment details.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient.user', 'doctor.user', 'doctor.specialty', 'payment']);

        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Administratively cancel an appointment.
     */
    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $oldStatus = $appointment->status;
        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
            'cancellation_reason' => 'Admin cancellation: ' . $request->input('reason'),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ADMIN_CANCEL_APPOINTMENT',
            'entity_type' => 'Appointment',
            'entity_id' => $appointment->id,
            'old_data' => ['status' => $oldStatus->value],
            'new_data' => ['status' => AppointmentStatus::CANCELLED->value, 'reason' => $request->input('reason')],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Appointment #' . $appointment->booking_code . ' was cancelled successfully.');
    }
}
