<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * System health check endpoint.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Throwable $e) {
            $dbConnected = false;
        }

        return $this->successResponse([
            'status' => 'healthy',
            'service' => 'HealthCare Integrated Medical Platform API',
            'version' => 'v1',
            'environment' => config('app.env'),
            'database' => $dbConnected ? 'connected' : 'disconnected',
            'counts' => [
                'specialties' => Specialty::count(),
                'doctors' => Doctor::count(),
                'users' => User::count(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 'HealthCare API is running');
    }

    /**
     * Idempotent system seeder endpoint for production initial database population.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function seed(Request $request): JsonResponse
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DatabaseSeeder',
                '--force' => true,
            ]);

            return $this->successResponse([
                'output' => Artisan::output(),
                'specialties_count' => Specialty::count(),
                'doctors_count' => Doctor::count(),
                'users_count' => User::count(),
            ], 'Production database seeded successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Seeding error: ' . $e->getMessage(), 500);
        }
    }
}
