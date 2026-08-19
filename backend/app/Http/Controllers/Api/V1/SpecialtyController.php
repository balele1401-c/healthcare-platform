<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SpecialtyResource;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    /**
     * List all medical specialties.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Specialty::query()->where('status', 'active');

        if ($request->filled('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $specialties = $query->withCount(['doctors' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        return $this->collectionResponse(
            SpecialtyResource::collection($specialties),
            'Specialties retrieved successfully.'
        );
    }

    /**
     * Retrieve a specific specialty.
     */
    public function show(Specialty $specialty): JsonResponse
    {
        $specialty->load(['doctors' => function ($q) {
            $q->where('status', 'active')->with(['user', 'specialty']);
        }]);

        return $this->successResponse(
            new SpecialtyResource($specialty),
            'Specialty details retrieved.'
        );
    }
}
