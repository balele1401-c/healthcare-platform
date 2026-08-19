<?php

namespace App\Http\Controllers\Web\Staff;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffAppointmentController extends Controller
{
    /**
     * Display a listing of appointments for clinic coordination.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $date = $request->query('date');
        $specialtyId = $request->query('specialty_id');

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

        if (! empty($specialtyId)) {
            $query->whereHas('doctor', fn ($dq) => $dq->where('specialty_id', $specialtyId));
        }

        $appointments = $query->latest('appointment_date')->latest('appointment_time')->paginate(10)->withQueryString();
        $specialties = Specialty::orderBy('name')->get();

        return view('staff.appointments.index', compact('appointments', 'specialties', 'search', 'status', 'date', 'specialtyId'));
    }

    /**
     * Display appointment coordination details.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient.user', 'doctor.user', 'doctor.specialty', 'payment']);

        return view('staff.appointments.show', compact('appointment'));
    }

    /**
     * Update appointment coordination status (Confirm / Cancel).
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:confirmed,cancelled'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $appointment->status;
        $newStatus = AppointmentStatus::from($request->input('status'));

        $appointment->update([
            'status' => $newStatus,
            'cancellation_reason' => $newStatus === AppointmentStatus::CANCELLED
                ? 'Staff coordination: ' . ($request->input('reason') ?? 'Operational cancellation')
                : $appointment->cancellation_reason,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'STAFF_UPDATE_APPOINTMENT_STATUS',
            'entity_type' => 'Appointment',
            'entity_id' => $appointment->id,
            'old_data' => ['status' => $oldStatus->value],
            'new_data' => ['status' => $newStatus->value, 'reason' => $request->input('reason')],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Appointment #' . $appointment->booking_code . ' status updated to ' . $newStatus->label() . '.');
    }
}
