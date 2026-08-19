<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PrescriptionResource;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PrescriptionController extends Controller
{
    /**
     * List prescriptions accessible to the authenticated role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            return $this->errorResponse('Unauthorized to access patient prescriptions.', 403);
        }

        $query = Prescription::query()
            ->with(['items.medicine', 'doctor.specialty', 'doctor.user', 'patient.user']);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        } elseif ($user->isDoctor()) {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $prescriptions = $query->latest('prescription_date')->paginate($perPage);

        return $this->paginatedResponse(
            PrescriptionResource::collection($prescriptions),
            'Prescriptions retrieved.'
        );
    }

    /**
     * Retrieve details of a specific prescription order.
     */
    public function show(Prescription $prescription): JsonResponse
    {
        Gate::authorize('view', $prescription);

        $prescription->load(['items.medicine', 'doctor.specialty', 'doctor.user', 'patient.user']);

        return $this->successResponse(
            new PrescriptionResource($prescription),
            'Prescription retrieved.'
        );
    }
}
