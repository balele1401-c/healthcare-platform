<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    /**
     * Display a paginated listing of doctor's appointments.
     */
    public function index(Request $request): View
    {
        $doctor = Auth::user()->doctor;

        $search = $request->query('search');
        $status = $request->query('status');
        $date = $request->query('date');

        $query = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'payment']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'ilike', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($date)) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->latest('appointment_date')->latest('appointment_time')->paginate(10)->withQueryString();

        return view('doctor.appointments.index', compact('appointments', 'search', 'status', 'date'));
    }

    /**
     * Display appointment details.
     */
    public function show(Appointment $appointment): View
    {
        $doctor = Auth::user()->doctor;

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You can only view clinical appointments assigned to your schedule.');
        }

        $appointment->load([
            'patient.user',
            'patient.healthMetrics' => fn ($q) => $q->latest('measured_at')->take(5),
            'payment',
        ]);

        return view('doctor.appointments.show', compact('appointment'));
    }

    /**
     * Update consultation appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You can only update appointments assigned to your schedule.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:confirmed,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $appointment->status;
        $newStatus = AppointmentStatus::from($request->input('status'));

        $appointment->update([
            'status' => $newStatus,
            'cancellation_reason' => $newStatus === AppointmentStatus::CANCELLED
                ? 'Doctor cancelled: ' . ($request->input('notes') ?? 'Clinical schedule conflict')
                : $appointment->cancellation_reason,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DOCTOR_UPDATE_APPOINTMENT_STATUS',
            'entity_type' => 'Appointment',
            'entity_id' => $appointment->id,
            'old_data' => ['status' => $oldStatus->value],
            'new_data' => ['status' => $newStatus->value, 'notes' => $request->input('notes')],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Appointment #' . $appointment->booking_code . ' status updated to ' . $newStatus->label() . '.');
    }
}
