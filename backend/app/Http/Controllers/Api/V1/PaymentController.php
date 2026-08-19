<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * List payment transaction records for the authenticated patient or admin.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            // Staff members can only view billing if from billing department
            $staff = $user->staff;
            if (! $staff || ! str_contains(strtolower($staff->department), 'billing')) {
                return $this->errorResponse('Unauthorized to access financial payment records.', 403);
            }
        }

        $query = Payment::query()->with(['appointment.doctor.specialty', 'patient.user']);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $payments = $query->latest('paid_at')->paginate($perPage);

        return $this->paginatedResponse(
            PaymentResource::collection($payments),
            'Payment records retrieved.'
        );
    }

    /**
     * Retrieve a specific payment receipt record.
     */
    public function show(Payment $payment, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isPatient()) {
            $patient = $user->patient;
            if (! $patient || $payment->patient_id !== $patient->id) {
                return $this->errorResponse('Unauthorized to access this payment receipt.', 403);
            }
        }

        $payment->load(['appointment.doctor.specialty', 'patient.user']);

        return $this->successResponse(
            new PaymentResource($payment),
            'Payment record retrieved.'
        );
    }
}
