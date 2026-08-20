<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user account (defaults to Patient role).
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated, $request) {
            $userRole = UserRole::PATIENT;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => $userRole,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => null,
            ]);

            if ($userRole === UserRole::PATIENT) {
                Patient::create([
                    'user_id' => $user->id,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'blood_type' => $validated['blood_type'] ?? null,
                ]);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'USER_REGISTERED',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'new_data' => ['email' => $user->email, 'role' => $user->role->value],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $user;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->value,
                'status' => $user->status->value,
                'avatar_url' => $user->avatar_url,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Patient registration successful.', 201);
    }

    /**
     * Authenticate user with credentials and issue a Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Invalid email or password credentials.', 401);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            return $this->errorResponse('Your account is currently inactive or suspended. Please contact hospital support.', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'USER_LOGIN',
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->value,
                'status' => $user->status->value,
                'avatar_url' => $user->avatar_url,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful.');
    }

    /**
     * Revoke the current access token on logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'USER_LOGOUT',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return $this->successResponse(null, 'Successfully logged out.');
    }

    /**
     * Retrieve authenticated user profile with associated role metadata.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isPatient()) {
            $user->load('patient');
        } elseif ($user->isDoctor()) {
            $user->load(['doctor.specialty']);
        } elseif ($user->isStaff()) {
            $user->load('staff');
        }

        return $this->successResponse($user, 'User profile retrieved.');
    }
}
