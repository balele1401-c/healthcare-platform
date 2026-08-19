<?php

namespace Tests\Feature;

use App\Enums\HealthMetricType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\HealthMetric;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HealthMetricApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $otherUser;
    private string $otherToken;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        $this->patientUser = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patient = Patient::create(['user_id' => $this->patientUser->id]);
        $this->patientToken = $this->patientUser->createToken('token')->plainTextToken;

        $this->otherUser = User::create([
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        Patient::create(['user_id' => $this->otherUser->id]);
        $this->otherToken = $this->otherUser->createToken('token')->plainTextToken;
    }

    public function test_patient_can_log_and_view_health_metric(): void
    {
        $payload = [
            'metric_type' => HealthMetricType::BLOOD_PRESSURE->value,
            'value' => 118.0,
            'secondary_value' => 76.0,
            'unit' => 'mmHg',
            'notes' => 'Morning resting reading',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/health-metrics', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.metric_type', HealthMetricType::BLOOD_PRESSURE->value)
            ->assertJsonPath('data.value', 118)
            ->assertJsonPath('data.secondary_value', 76);

        $metricId = $response->json('data.id');

        $detailResponse = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->getJson("/api/v1/health-metrics/{$metricId}");

        $detailResponse->assertStatus(200)
            ->assertJsonPath('data.value', 118);
    }

    public function test_patient_can_update_and_delete_own_metric(): void
    {
        $metric = HealthMetric::create([
            'patient_id' => $this->patient->id,
            'metric_type' => HealthMetricType::WEIGHT,
            'value' => 65.0,
            'unit' => 'kg',
            'measured_at' => now(),
        ]);

        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->putJson("/api/v1/health-metrics/{$metric->id}", [
                'value' => 64.5,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.value', 64.5);

        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->deleteJson("/api/v1/health-metrics/{$metric->id}");

        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('health_metrics', ['id' => $metric->id]);
    }

    public function test_patient_cannot_access_or_modify_other_patients_metrics(): void
    {
        $metric = HealthMetric::create([
            'patient_id' => $this->patient->id,
            'metric_type' => HealthMetricType::HEART_RATE,
            'value' => 70.0,
            'unit' => 'bpm',
            'measured_at' => now(),
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->otherToken)
            ->getJson("/api/v1/health-metrics/{$metric->id}")
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $this->otherToken)
            ->putJson("/api/v1/health-metrics/{$metric->id}", ['value' => 80.0])
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $this->otherToken)
            ->deleteJson("/api/v1/health-metrics/{$metric->id}")
            ->assertStatus(403);
    }
}
