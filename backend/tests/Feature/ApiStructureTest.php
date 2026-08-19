<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiStructureTest extends TestCase
{
    /**
     * Test that non-existent API routes return 404 with standard JSON format.
     */
    public function test_non_existent_api_endpoint_returns_json_404(): void
    {
        $response = $this->getJson('/api/v1/invalid-route');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource or endpoint not found.',
            ]);
    }

    /**
     * Test that protected API endpoints return 401 unauthenticated JSON when called without token.
     */
    public function test_protected_api_endpoint_returns_json_401(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated access. Please provide a valid Bearer token.',
            ]);
    }
}
