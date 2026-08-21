<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\HealthMetricController;
use App\Http\Controllers\Api\V1\MedicalRecordController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PrescriptionController;
use App\Http\Controllers\Api\V1\SpecialtyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — HealthCare Integrated Medical Platform
|--------------------------------------------------------------------------
|
| Versioned REST API endpoints for Patient App, Admin, Doctor & Staff.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // 1. Health Probe & Safe Production Database Seeder
    Route::get('/health', [HealthController::class, 'check'])->name('health');
    Route::post('/system/seed', [HealthController::class, 'seed'])->name('system.seed');

    // 2. Public / Discovery Endpoints
    Route::prefix('specialties')->name('specialties.')->group(function () {
        Route::get('/', [SpecialtyController::class, 'index'])->name('index');
        Route::get('/{specialty}', [SpecialtyController::class, 'show'])->name('show');
    });

    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->name('index');
        Route::get('/{doctor}', [DoctorController::class, 'show'])->name('show');
        Route::get('/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('schedules');
    });

    // 3. Authentication Endpoints (Rate limited)
    Route::prefix('auth')->name('auth.')->middleware('throttle:60,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
        });
    });

    // 4. Authenticated Clinical & Platform Resources
    Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {

        // User Context
        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user(),
            ]);
        })->name('user');

        // Patient Dedicated Endpoints (Protected for Patient, Admin, Owner)
        Route::prefix('patient')->middleware('role:patient,admin,owner')->name('patient.')->group(function () {
            Route::get('/profile', [PatientController::class, 'profile'])->name('profile');
            Route::put('/profile', [PatientController::class, 'updateProfile'])->name('profile.update');
            Route::get('/appointments', [PatientController::class, 'appointments'])->name('appointments');
            Route::get('/medical-records', [PatientController::class, 'medicalRecords'])->name('medical-records');
            Route::get('/prescriptions', [PatientController::class, 'prescriptions'])->name('prescriptions');
            Route::get('/health-metrics', [PatientController::class, 'healthMetrics'])->name('health-metrics');
            Route::get('/notifications', [PatientController::class, 'notifications'])->name('notifications');
        });

        // Doctor Dedicated Endpoints (Protected for Doctor, Admin, Owner)
        Route::prefix('doctor')->middleware('role:doctor,admin,owner')->name('doctor.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'doctor'])->name('dashboard');
        });

        // Staff Dedicated Endpoints (Protected for Staff, Admin, Owner)
        Route::prefix('staff')->middleware('role:staff,admin,owner')->name('staff.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'staff'])->name('dashboard');
        });

        // Admin Dedicated Endpoints (Protected for Admin, Owner)
        Route::prefix('admin')->middleware('role:admin,owner')->name('admin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'admin'])->name('dashboard');
        });

        // Owner Dedicated Endpoints (Protected for Owner & Admin)
        Route::prefix('owner')->middleware('role:owner,admin')->name('owner.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'owner'])->name('dashboard');
        });

        // Appointments & Consultations
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [AppointmentController::class, 'index'])->name('index');
            Route::post('/', [AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
            Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
            Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        });

        // Medical Records & Clinical Summaries
        Route::prefix('medical-records')->name('medical-records.')->group(function () {
            Route::get('/', [MedicalRecordController::class, 'index'])->name('index');
            Route::get('/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('show');
        });

        // Prescriptions & Medications
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::get('/', [PrescriptionController::class, 'index'])->name('index');
            Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        });

        // Health Metrics Tracker
        Route::prefix('health-metrics')->name('health-metrics.')->group(function () {
            Route::get('/', [HealthMetricController::class, 'index'])->name('index');
            Route::post('/', [HealthMetricController::class, 'store'])->name('store');
            Route::get('/{healthMetric}', [HealthMetricController::class, 'show'])->name('show');
            Route::put('/{healthMetric}', [HealthMetricController::class, 'update'])->name('update');
            Route::delete('/{healthMetric}', [HealthMetricController::class, 'destroy'])->name('destroy');
        });

        // In-App Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        });

        // Teleconsultation & Chat Channels
        Route::prefix('conversations')->name('conversations.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::post('/', [ChatController::class, 'store'])->name('store');
            Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
            Route::get('/{conversation}/messages', [ChatController::class, 'messages'])->name('messages');
            Route::post('/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('messages.send');
        });

        // Billing & Payment Invoices
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::post('/', [PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
        });
    });

    // 5. Unauthenticated Payment Webhook Endpoint (Protected by Provider Signature)
    Route::post('/payments/webhook/{provider}', [PaymentController::class, 'webhook'])->name('payments.webhook');
});
