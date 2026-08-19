<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
            'timestamp' => now()->toIso8601String(),
        ], 'HealthCare API is running');
    }
}
