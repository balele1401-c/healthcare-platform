<?php

use App\Http\Controllers\Web\Admin\AdminAppointmentController;
use App\Http\Controllers\Web\Admin\AdminAuditLogController;
use App\Http\Controllers\Web\Admin\AdminAuthController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminDoctorController;
use App\Http\Controllers\Web\Admin\AdminMedicalRecordController;
use App\Http\Controllers\Web\Admin\AdminNotificationController;
use App\Http\Controllers\Web\Admin\AdminPatientController;
use App\Http\Controllers\Web\Admin\AdminPaymentController;
use App\Http\Controllers\Web\Admin\AdminPrescriptionController;
use App\Http\Controllers\Web\Admin\AdminProfileController;
use App\Http\Controllers\Web\Admin\AdminSpecialtyController;
use App\Http\Controllers\Web\Doctor\DoctorAppointmentController;
use App\Http\Controllers\Web\Doctor\DoctorAuthController;
use App\Http\Controllers\Web\Doctor\DoctorChatController;
use App\Http\Controllers\Web\Doctor\DoctorDashboardController;
use App\Http\Controllers\Web\Doctor\DoctorHealthMetricController;
use App\Http\Controllers\Web\Doctor\DoctorMedicalRecordController;
use App\Http\Controllers\Web\Doctor\DoctorNotificationController;
use App\Http\Controllers\Web\Doctor\DoctorPatientController;
use App\Http\Controllers\Web\Doctor\DoctorPrescriptionController;
use App\Http\Controllers\Web\Doctor\DoctorProfileController;
use App\Http\Controllers\Web\Doctor\DoctorScheduleController;
use App\Http\Controllers\Web\Staff\StaffActivityController;
use App\Http\Controllers\Web\Staff\StaffAppointmentController;
use App\Http\Controllers\Web\Staff\StaffAuthController;
use App\Http\Controllers\Web\Staff\StaffDashboardController;
use App\Http\Controllers\Web\Staff\StaffDoctorController;
use App\Http\Controllers\Web\Staff\StaffNotificationController;
use App\Http\Controllers\Web\Staff\StaffPatientController;
use App\Http\Controllers\Web\Staff\StaffPaymentController;
use App\Http\Controllers\Web\Staff\StaffProfileController;
use App\Http\Controllers\Web\Staff\StaffScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — HealthCare Integrated Medical Platform
|--------------------------------------------------------------------------
|
| Dedicated Administrative, Doctor Clinical, and Staff Operations Web Portals.
|
*/

Route::get('/', [AdminAuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');

/*
|--------------------------------------------------------------------------
| 1. Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Guest Routes
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes (Requires authenticated user with ADMIN role)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.view');

        // Patients Management
        Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [AdminPatientController::class, 'show'])->name('patients.show');

        // Doctors Management
        Route::get('/doctors', [AdminDoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [AdminDoctorController::class, 'show'])->name('doctors.show');

        // Specialties Overview
        Route::get('/specialties', [AdminSpecialtyController::class, 'index'])->name('specialties.index');

        // Appointments Management
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Medical Records Overview
        Route::get('/medical-records', [AdminMedicalRecordController::class, 'index'])->name('medical-records.index');
        Route::get('/medical-records/{medicalRecord}', [AdminMedicalRecordController::class, 'show'])->name('medical-records.show');

        // Prescriptions Overview
        Route::get('/prescriptions', [AdminPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/{prescription}', [AdminPrescriptionController::class, 'show'])->name('prescriptions.show');

        // Payments & Billing Overview
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

        // Notifications & System Alerts
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // Audit Trail Logs
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

        // Admin Profile
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    });
});

/*
|--------------------------------------------------------------------------
| 2. Doctor Clinical Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('doctor')->name('doctor.')->group(function () {
    // Doctor Guest Routes
    Route::get('/login', [DoctorAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [DoctorAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [DoctorAuthController::class, 'logout'])->name('logout');

    // Protected Doctor Routes (Requires authenticated user with DOCTOR role)
    Route::middleware(['auth', 'doctor'])->group(function () {
        Route::get('/', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard.view');

        // Appointments Management
        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Patients List & Clinical History (Assigned only)
        Route::get('/patients', [DoctorPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [DoctorPatientController::class, 'show'])->name('patients.show');

        // Medical Records
        Route::get('/medical-records', [DoctorMedicalRecordController::class, 'index'])->name('medical-records.index');
        Route::get('/medical-records/create', [DoctorMedicalRecordController::class, 'create'])->name('medical-records.create');
        Route::post('/medical-records', [DoctorMedicalRecordController::class, 'store'])->name('medical-records.store');
        Route::get('/medical-records/{medicalRecord}', [DoctorMedicalRecordController::class, 'show'])->name('medical-records.show');

        // Digital Prescriptions
        Route::get('/prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::get('/prescriptions/create', [DoctorPrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/prescriptions', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/prescriptions/{prescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');

        // Health Metrics Monitoring
        Route::get('/health-metrics', [DoctorHealthMetricController::class, 'index'])->name('health-metrics.index');

        // Schedules & Weekly Consultation Slots
        Route::get('/schedules', [DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::post('/schedules', [DoctorScheduleController::class, 'store'])->name('schedules.store');
        Route::post('/schedules/{schedule}/toggle', [DoctorScheduleController::class, 'toggle'])->name('schedules.toggle');

        // Teleconsultation / REST Chat
        Route::get('/chat', [DoctorChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [DoctorChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{conversation}/messages', [DoctorChatController::class, 'sendMessage'])->name('chat.send');

        // Clinical Notifications
        Route::get('/notifications', [DoctorNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [DoctorNotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // Doctor Practitioner Profile
        Route::get('/profile', [DoctorProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| 3. Staff Operations Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->group(function () {
    // Staff Guest Routes
    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');

    // Protected Staff Routes (Requires authenticated user with STAFF role)
    Route::middleware(['auth', 'staff'])->group(function () {
        Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard.view');

        // Appointment Coordination
        Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [StaffAppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/status', [StaffAppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Patient Intake & Registration
        Route::get('/patients', [StaffPatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [StaffPatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [StaffPatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [StaffPatientController::class, 'show'])->name('patients.show');

        // Doctor Directory & Roster
        Route::get('/doctors', [StaffDoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [StaffDoctorController::class, 'show'])->name('doctors.show');

        // Doctor Schedules Coordination
        Route::get('/schedules', [StaffScheduleController::class, 'index'])->name('schedules.index');

        // Payments & Billing Visibility
        Route::get('/payments', [StaffPaymentController::class, 'index'])->name('payments.index');

        // Operational Activity Logs
        Route::get('/activity', [StaffActivityController::class, 'index'])->name('activity.index');

        // Staff Notifications
        Route::get('/notifications', [StaffNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [StaffNotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // Staff Profile
        Route::get('/profile', [StaffProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    });
});
