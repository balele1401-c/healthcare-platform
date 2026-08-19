<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MedicalRecordController extends Controller
{
    /**
     * List medical records filtered by role authorization.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Staff members are barred from clinical medical records per HIPAA/clinical policy
        if ($user->isStaff()) {
            return $this->errorResponse('Unauthorized to access clinical medical records.', 403);
        }

        $query = MedicalRecord::query()
            ->with(['vitalSigns', 'prescriptions.items.medicine', 'doctor.specialty', 'doctor.user', 'patient.user']);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        } elseif ($user->isDoctor()) {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        }

        if ($request->filled('from')) {
            $query->whereDate('visit_date', '>=', $request->query('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('visit_date', '<=', $request->query('to'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $records = $query->latest('visit_date')->paginate($perPage);

        return $this->paginatedResponse(
            MedicalRecordResource::collection($records),
            'Medical records retrieved.'
        );
    }

    /**
     * Retrieve a specific clinical medical record.
     */
    public function show(MedicalRecord $medicalRecord): JsonResponse
    {
        Gate::authorize('view', $medicalRecord);

        $medicalRecord->load(['vitalSigns', 'prescriptions.items.medicine', 'doctor.specialty', 'doctor.user', 'patient.user']);

        return $this->successResponse(
            new MedicalRecordResource($medicalRecord),
            'Medical record retrieved.'
        );
    }
}
