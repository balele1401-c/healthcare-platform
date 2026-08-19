<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test that the API health check endpoint returns 200 with standard JSON structure.
     */
    public function test_api_v1_health_endpoint_returns_success(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'HealthCare API is running',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'status',
                    'service',
                    'version',
                    'environment',
                    'database',
                    'timestamp',
                ],
            ]);
    }
}
