<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DoctorResource;
use App\Http\Resources\V1\DoctorScheduleResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Search and list medical specialists with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::query()
            ->with(['user', 'specialty', 'schedules' => fn ($q) => $q->where('is_available', true)])
            ->where('status', 'active');

        // Specialty filter by ID
        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->query('specialty_id'));
        }

        // Specialty filter by slug
        if ($request->filled('specialty')) {
            $slug = $request->query('specialty');
            $query->whereHas('specialty', fn ($q) => $q->where('slug', $slug));
        }

        // Search query (doctor name, bio, or facility)
        if ($request->filled('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($uq) => $uq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]))
                  ->orWhereRaw('LOWER(biography) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(facility) LIKE ?', ["%{$search}%"]);
            });
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $doctors = $query->orderByDesc('rating')->paginate($perPage);

        return $this->paginatedResponse(
            DoctorResource::collection($doctors),
            'Doctors retrieved successfully.'
        );
    }

    /**
     * Retrieve a specific doctor profile and clinical metadata.
     */
    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load(['user', 'specialty', 'schedules' => fn ($q) => $q->where('is_available', true)]);

        return $this->successResponse(
            new DoctorResource($doctor),
            'Doctor profile retrieved.'
        );
    }

    /**
     * Retrieve available consultation schedules for a specific doctor.
     */
    public function schedules(Doctor $doctor): JsonResponse
    {
        $schedules = $doctor->schedules()
            ->where('is_available', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return $this->collectionResponse(
            DoctorScheduleResource::collection($schedules),
            'Doctor consultation schedules retrieved.'
        );
    }
}
