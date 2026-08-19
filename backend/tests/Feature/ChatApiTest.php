<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ChatConversation;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $docUser;
    private Doctor $doctor;
    private string $doctorToken;

    private User $unauthorizedUser;
    private string $unauthorizedToken;

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

        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $this->docUser = User::create([
            'name' => 'Dr. Emily Chen',
            'email' => 'chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->docUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-100',
        ]);
        $this->doctorToken = $this->docUser->createToken('token')->plainTextToken;

        $this->unauthorizedUser = User::create([
            'name' => 'Intruder User',
            'email' => 'intruder@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        Patient::create(['user_id' => $this->unauthorizedUser->id]);
        $this->unauthorizedToken = $this->unauthorizedUser->createToken('token')->plainTextToken;
    }

    public function test_can_start_chat_and_exchange_messages(): void
    {
        // 1. Patient starts conversation with Dr. Chen
        $initResponse = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/conversations', [
                'doctor_id' => $this->doctor->id,
                'initial_message' => 'Hello Dr. Chen, quick question about my medication.',
            ]);

        $initResponse->assertStatus(201)
            ->assertJsonPath('data.doctor_id', $this->doctor->id);

        $convId = $initResponse->json('data.id');

        // 2. Doctor views messages
        $messagesResponse = $this->withHeader('Authorization', 'Bearer ' . $this->doctorToken)
            ->getJson("/api/v1/conversations/{$convId}/messages");

        $messagesResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.message', 'Hello Dr. Chen, quick question about my medication.');

        // 3. Doctor sends reply
        $replyResponse = $this->withHeader('Authorization', 'Bearer ' . $this->doctorToken)
            ->postJson("/api/v1/conversations/{$convId}/messages", [
                'message' => 'Hello Sarah, please take your morning tablet as scheduled.',
            ]);

        $replyResponse->assertStatus(201)
            ->assertJsonPath('data.message', 'Hello Sarah, please take your morning tablet as scheduled.');
    }

    public function test_unauthorized_user_cannot_access_or_send_messages_to_conversation(): void
    {
        $conv = ChatConversation::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'active',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->unauthorizedToken)
            ->getJson("/api/v1/conversations/{$conv->id}")
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $this->unauthorizedToken)
            ->getJson("/api/v1/conversations/{$conv->id}/messages")
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $this->unauthorizedToken)
            ->postJson("/api/v1/conversations/{$conv->id}/messages", ['message' => 'Hacking in'])
            ->assertStatus(403);
    }
}
