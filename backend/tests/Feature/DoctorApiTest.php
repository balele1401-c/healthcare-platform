<?php

namespace Tests\Feature;

use App\Enums\ConsultationType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DoctorApiTest extends TestCase
{
    use RefreshDatabase;

    private Specialty $cardiology;
    private Specialty $dermatology;
    private Doctor $doc1;
    private Doctor $doc2;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        $this->cardiology = Specialty::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'description' => 'Heart and vascular diseases.',
        ]);

        $this->dermatology = Specialty::create([
            'name' => 'Dermatology',
            'slug' => 'dermatology',
            'description' => 'Skin conditions.',
        ]);

        $u1 = User::create([
            'name' => 'Dr. Emily Chen',
            'email' => 'dr.chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->doc1 = Doctor::create([
            'user_id' => $u1->id,
            'specialty_id' => $this->cardiology->id,
            'license_number' => 'MD-1001',
            'biography' => 'Experienced cardiologist.',
            'consultation_fee' => 75.00,
            'rating' => 4.9,
            'status' => 'active',
        ]);

        DoctorSchedule::create([
            'doctor_id' => $this->doc1->id,
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'is_available' => true,
        ]);

        $u2 = User::create([
            'name' => 'Dr. Marcus Vance',
            'email' => 'dr.vance@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->doc2 = Doctor::create([
            'user_id' => $u2->id,
            'specialty_id' => $this->dermatology->id,
            'license_number' => 'MD-1002',
            'biography' => 'Expert dermatologist.',
            'consultation_fee' => 65.00,
            'rating' => 4.8,
            'status' => 'active',
        ]);
    }

    public function test_can_list_doctors(): void
    {
        $response = $this->getJson('/api/v1/doctors');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_doctors_by_specialty(): void
    {
        $response = $this->getJson('/api/v1/doctors?specialty_id=' . $this->cardiology->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Dr. Emily Chen');
    }

    public function test_can_search_doctors(): void
    {
        $response = $this->getJson('/api/v1/doctors?search=Vance');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Dr. Marcus Vance');
    }

    public function test_can_get_doctor_detail_and_schedules(): void
    {
        $response = $this->getJson('/api/v1/doctors/' . $this->doc1->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Dr. Emily Chen')
            ->assertJsonPath('data.consultation_fee', 75);

        $schedulesResponse = $this->getJson('/api/v1/doctors/' . $this->doc1->id . '/schedules');
        $schedulesResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
