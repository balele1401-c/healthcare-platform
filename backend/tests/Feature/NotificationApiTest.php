<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private string $tokenA;

    private User $userB;
    private string $tokenB;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        $this->userA = User::create([
            'name' => 'User A',
            'email' => 'user.a@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->tokenA = $this->userA->createToken('token')->plainTextToken;

        $this->userB = User::create([
            'name' => 'User B',
            'email' => 'user.b@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->tokenB = $this->userB->createToken('token')->plainTextToken;
    }

    public function test_user_can_list_and_mark_notifications_as_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->userA->id,
            'title' => 'Appointment Reminder',
            'message' => 'Consultation tomorrow.',
            'notification_type' => 'appointment',
        ]);

        $listResponse = $this->withHeader('Authorization', 'Bearer ' . $this->tokenA)
            ->getJson('/api/v1/notifications');

        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.is_read', false);

        $readResponse = $this->withHeader('Authorization', 'Bearer ' . $this->tokenA)
            ->postJson("/api/v1/notifications/{$notif->id}/read");

        $readResponse->assertStatus(200)
            ->assertJsonPath('data.is_read', true);
    }

    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        $notif = Notification::create([
            'user_id' => $this->userA->id,
            'title' => 'Private Alert',
            'message' => 'Your prescription is ready.',
            'notification_type' => 'prescription',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenB)
            ->postJson("/api/v1/notifications/{$notif->id}/read");

        $response->assertStatus(403);
    }
}
