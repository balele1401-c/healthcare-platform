<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CancelAppointmentRequest;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\V1\AppointmentResource;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * List appointments filtered by role, status, and dates.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Appointment::query()->with(['doctor.specialty', 'doctor.user', 'patient.user', 'payment']);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        } elseif ($user->isDoctor()) {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->query('date'));
        }

        if ($request->filled('doctor_id') && ! $user->isDoctor()) {
            $query->where('doctor_id', $request->query('doctor_id'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $appointments = $query->latest('appointment_date')->paginate($perPage);

        return $this->paginatedResponse(
            AppointmentResource::collection($appointments),
            'Appointments retrieved successfully.'
        );
    }

    /**
     * Create a new consultation appointment booking.
     */
    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $appointment = DB::transaction(function () use ($validated, $patient, $doctor, $user, $request) {
            $consultationFee = (float) $doctor->consultation_fee;
            $serviceFee = 5.00;
            $totalAmount = $consultationFee + $serviceFee;

            $bookingCode = 'APT-' . strtoupper(Str::random(6));

            $appointment = Appointment::create([
                'booking_code' => $bookingCode,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'doctor_schedule_id' => $validated['doctor_schedule_id'] ?? null,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'status' => AppointmentStatus::PENDING,
                'consultation_type' => $validated['consultation_type'],
                'facility' => $validated['facility'] ?? $doctor->facility,
                'consultation_fee' => $consultationFee,
                'service_fee' => $serviceFee,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'CREATE_APPOINTMENT',
                'entity_type' => 'Appointment',
                'entity_id' => $appointment->id,
                'new_data' => ['booking_code' => $bookingCode, 'total' => $totalAmount],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $appointment->load(['doctor.specialty', 'doctor.user', 'patient.user']);
        });

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment booked successfully.',
            201
        );
    }

    /**
     * Retrieve appointment details.
     */
    public function show(Appointment $appointment): JsonResponse
    {
        Gate::authorize('view', $appointment);

        $appointment->load(['doctor.specialty', 'doctor.user', 'patient.user', 'payment']);

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment details retrieved.'
        );
    }

    /**
     * Update appointment details or reschedule slot.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        Gate::authorize('update', $appointment);

        $validated = $request->validated();
        $appointment->update(array_filter($validated));

        return $this->successResponse(
            new AppointmentResource($appointment->fresh(['doctor.specialty', 'doctor.user', 'patient.user'])),
            'Appointment updated successfully.'
        );
    }

    /**
     * Cancel an existing appointment booking.
     */
    public function cancel(CancelAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        Gate::authorize('cancel', $appointment);

        if ($appointment->status === AppointmentStatus::CANCELLED) {
            return $this->errorResponse('This appointment is already cancelled.', 400);
        }

        if ($appointment->status === AppointmentStatus::COMPLETED) {
            return $this->errorResponse('Completed appointments cannot be cancelled.', 400);
        }

        $validated = $request->validated();

        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'CANCEL_APPOINTMENT',
            'entity_type' => 'Appointment',
            'entity_id' => $appointment->id,
            'new_data' => ['reason' => $validated['cancellation_reason']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return $this->successResponse(
            new AppointmentResource($appointment->fresh(['doctor.specialty', 'doctor.user', 'patient.user'])),
            'Appointment cancelled successfully.'
        );
    }
}
