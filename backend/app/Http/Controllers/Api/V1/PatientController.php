<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\UpdatePatientProfileRequest;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Resources\V1\HealthMetricResource;
use App\Http\Resources\V1\MedicalRecordResource;
use App\Http\Resources\V1\NotificationResource;
use App\Http\Resources\V1\PatientResource;
use App\Http\Resources\V1\PrescriptionResource;
use App\Models\AuditLog;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    /**
     * Get the authenticated patient profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
        $patient->load('user');

        return $this->successResponse(
            new PatientResource($patient),
            'Patient profile retrieved.'
        );
    }

    /**
     * Update the authenticated patient profile.
     */
    public function updateProfile(UpdatePatientProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $patient = DB::transaction(function () use ($user, $validated, $request) {
            // Update User fields if provided
            $userFields = array_filter([
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'avatar_url' => $validated['avatar_url'] ?? null,
            ]);

            if (! empty($userFields)) {
                $user->update($userFields);
            }

            // Update Patient demographics
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);

            $patientFields = array_intersect_key($validated, array_flip([
                'date_of_birth',
                'gender',
                'blood_type',
                'height_cm',
                'weight_kg',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'allergies',
                'medical_history_summary',
            ]));

            if (! empty($patientFields)) {
                $patient->update($patientFields);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'UPDATE_PATIENT_PROFILE',
                'entity_type' => 'Patient',
                'entity_id' => $patient->id,
                'new_data' => $patientFields,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $patient->fresh()->load('user');
        });

        return $this->successResponse(
            new PatientResource($patient),
            'Patient profile updated successfully.'
        );
    }

    /**
     * Get authenticated patient's appointments.
     */
    public function appointments(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (! $patient) {
            return $this->collectionResponse([], 'No patient profile found.');
        }

        $query = $patient->appointments()
            ->with(['doctor.specialty', 'doctor.user', 'payment'])
            ->latest('appointment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $appointments = $query->paginate($perPage);

        return $this->paginatedResponse(
            AppointmentResource::collection($appointments),
            'Patient appointments retrieved.'
        );
    }

    /**
     * Get authenticated patient's medical records.
     */
    public function medicalRecords(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (! $patient) {
            return $this->collectionResponse([], 'No patient profile found.');
        }

        $query = $patient->medicalRecords()
            ->with(['vitalSigns', 'prescriptions.items.medicine', 'doctor.specialty', 'doctor.user'])
            ->latest('visit_date');

        $perPage = min((int) $request->query('per_page', 15), 50);
        $records = $query->paginate($perPage);

        return $this->paginatedResponse(
            MedicalRecordResource::collection($records),
            'Patient medical records retrieved.'
        );
    }

    /**
     * Get authenticated patient's prescriptions.
     */
    public function prescriptions(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (! $patient) {
            return $this->collectionResponse([], 'No patient profile found.');
        }

        $query = $patient->prescriptions()
            ->with(['items.medicine', 'doctor.specialty', 'doctor.user'])
            ->latest('prescription_date');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $prescriptions = $query->paginate($perPage);

        return $this->paginatedResponse(
            PrescriptionResource::collection($prescriptions),
            'Patient prescriptions retrieved.'
        );
    }

    /**
     * Get authenticated patient's health metrics.
     */
    public function healthMetrics(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        if (! $patient) {
            return $this->collectionResponse([], 'No patient profile found.');
        }

        $query = $patient->healthMetrics()->latest('measured_at');

        if ($request->filled('metric_type')) {
            $query->where('metric_type', $request->query('metric_type'));
        }

        if ($request->filled('from')) {
            $query->where('measured_at', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->where('measured_at', '<=', $request->query('to'));
        }

        $perPage = min((int) $request->query('per_page', 30), 100);
        $metrics = $query->paginate($perPage);

        return $this->paginatedResponse(
            HealthMetricResource::collection($metrics),
            'Patient health metrics retrieved.'
        );
    }

    /**
     * Get authenticated patient's notifications.
     */
    public function notifications(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()->latest();

        if ($request->has('read')) {
            $isRead = filter_var($request->query('read'), FILTER_VALIDATE_BOOLEAN);
            $query->where(fn ($q) => $isRead ? $q->whereNotNull('read_at') : $q->whereNull('read_at'));
        }

        $perPage = min((int) $request->query('per_page', 20), 50);
        $notifications = $query->paginate($perPage);

        return $this->paginatedResponse(
            NotificationResource::collection($notifications),
            'Notifications retrieved.'
        );
    }
}
