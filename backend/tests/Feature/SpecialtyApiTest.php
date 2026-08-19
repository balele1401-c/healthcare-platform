<?php

namespace Tests\Feature;

use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecialtyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_search_specialties(): void
    {
        Specialty::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'description' => 'Heart and vascular diseases.',
        ]);

        Specialty::create([
            'name' => 'Pediatrics',
            'slug' => 'pediatrics',
            'description' => 'Child health.',
        ]);

        $response = $this->getJson('/api/v1/specialties');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data');

        $searchResponse = $this->getJson('/api/v1/specialties?search=Pediatr');
        $searchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Pediatrics');
    }
}
