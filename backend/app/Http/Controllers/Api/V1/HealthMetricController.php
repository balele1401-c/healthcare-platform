<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HealthMetricType;
use App\Http\Controllers\Controller;
use App\Http\Requests\HealthMetric\CreateHealthMetricRequest;
use App\Http\Requests\HealthMetric\UpdateHealthMetricRequest;
use App\Http\Resources\V1\HealthMetricResource;
use App\Models\AuditLog;
use App\Models\HealthMetric;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HealthMetricController extends Controller
{
    /**
     * List health metrics logged for the patient.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isStaff()) {
            return $this->errorResponse('Unauthorized to access patient biometric vitals.', 403);
        }

        $query = HealthMetric::query();

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        } elseif ($user->isDoctor() || $user->isAdmin()) {
            if ($request->filled('patient_id')) {
                $query->where('patient_id', $request->query('patient_id'));
            }
        }

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
        $metrics = $query->latest('measured_at')->paginate($perPage);

        return $this->paginatedResponse(
            HealthMetricResource::collection($metrics),
            'Health metrics retrieved.'
        );
    }

    /**
     * Log a new biometric reading.
     */
    public function store(CreateHealthMetricRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);

        $metricType = HealthMetricType::from($validated['metric_type']);
        $unit = $validated['unit'] ?? $metricType->defaultUnit();

        $metric = HealthMetric::create([
            'patient_id' => $patient->id,
            'metric_type' => $metricType,
            'value' => $validated['value'],
            'secondary_value' => $validated['secondary_value'] ?? null,
            'unit' => $unit,
            'measured_at' => $validated['measured_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LOG_HEALTH_METRIC',
            'entity_type' => 'HealthMetric',
            'entity_id' => $metric->id,
            'new_data' => ['type' => $metricType->value, 'value' => $metric->value],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return $this->successResponse(
            new HealthMetricResource($metric),
            'Health metric recorded successfully.',
            201
        );
    }

    /**
     * View a specific health metric entry.
     */
    public function show(HealthMetric $healthMetric): JsonResponse
    {
        Gate::authorize('view', $healthMetric);

        return $this->successResponse(
            new HealthMetricResource($healthMetric),
            'Health metric details retrieved.'
        );
    }

    /**
     * Update an existing health metric entry.
     */
    public function update(UpdateHealthMetricRequest $request, HealthMetric $healthMetric): JsonResponse
    {
        Gate::authorize('create', $healthMetric);

        $validated = $request->validated();
        $healthMetric->update(array_filter($validated, fn ($val) => $val !== null));

        return $this->successResponse(
            new HealthMetricResource($healthMetric),
            'Health metric updated successfully.'
        );
    }

    /**
     * Delete an existing health metric entry.
     */
    public function destroy(HealthMetric $healthMetric): JsonResponse
    {
        Gate::authorize('delete', $healthMetric);

        $healthMetric->delete();

        return $this->successResponse(null, 'Health metric deleted successfully.');
    }
}
