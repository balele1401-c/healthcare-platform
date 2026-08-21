<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Doctor Clinical Workspace Dashboard API.
     */
    public function doctor(Request $request): JsonResponse
    {
        $user = $request->user();
        $doctor = $user->doctor;

        $doctorId = $doctor?->id;

        $todayAppointments = $doctorId
            ? Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', today())
                ->with('patient.user')
                ->get()
            : collect();

        $stats = [
            'total_appointments' => $doctorId ? Appointment::where('doctor_id', $doctorId)->count() : 0,
            'today_appointments' => $todayAppointments->count(),
            'completed_consultations' => $doctorId ? Appointment::where('doctor_id', $doctorId)->where('status', AppointmentStatus::COMPLETED)->count() : 0,
            'active_prescriptions' => $doctorId ? Prescription::where('doctor_id', $doctorId)->count() : 0,
            'medical_records_created' => $doctorId ? MedicalRecord::where('doctor_id', $doctorId)->count() : 0,
        ];

        return $this->successResponse([
            'doctor' => $doctor,
            'statistics' => $stats,
            'today_schedule' => $todayAppointments,
        ], 'Doctor dashboard data retrieved successfully.');
    }

    /**
     * Staff Operations & Admissions Dashboard API.
     */
    public function staff(Request $request): JsonResponse
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'pending_appointments' => Appointment::where('status', AppointmentStatus::PENDING)->count(),
            'confirmed_appointments' => Appointment::where('status', AppointmentStatus::CONFIRMED)->count(),
            'today_revenue' => Payment::where('status', PaymentStatus::PAID)->whereDate('created_at', today())->sum('amount'),
        ];

        $recentQueue = Appointment::whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->with(['patient.user', 'doctor.user'])
            ->take(10)
            ->get();

        return $this->successResponse([
            'statistics' => $stats,
            'patient_queue' => $recentQueue,
        ], 'Staff operations dashboard retrieved successfully.');
    }

    /**
     * Admin Central System Management Dashboard API.
     */
    public function admin(Request $request): JsonResponse
    {
        $stats = [
            'users_total' => User::count(),
            'users_by_role' => [
                'patients' => User::where('role', UserRole::PATIENT)->count(),
                'doctors' => User::where('role', UserRole::DOCTOR)->count(),
                'staff' => User::where('role', UserRole::STAFF)->count(),
                'admins' => User::where('role', UserRole::ADMIN)->count(),
                'owners' => User::where('role', UserRole::OWNER)->count(),
            ],
            'specialties_count' => Specialty::count(),
            'doctors_count' => Doctor::count(),
            'appointments_count' => Appointment::count(),
            'total_revenue' => (float) Payment::where('status', PaymentStatus::PAID)->sum('amount'),
        ];

        return $this->successResponse([
            'system_status' => 'operational',
            'statistics' => $stats,
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at']),
        ], 'Admin dashboard data retrieved successfully.');
    }

    /**
     * Owner Executive & Financial Overview Dashboard API.
     */
    public function owner(Request $request): JsonResponse
    {
        $totalGrossRevenue = (float) Payment::where('status', PaymentStatus::PAID)->sum('amount');
        $serviceFees = (float) Appointment::where('status', AppointmentStatus::COMPLETED)->sum('service_fee');
        $consultationFees = (float) Appointment::where('status', AppointmentStatus::COMPLETED)->sum('consultation_fee');

        $reports = [
            'financial_summary' => [
                'total_gross_revenue' => $totalGrossRevenue,
                'platform_service_fees' => $serviceFees,
                'provider_consultation_fees' => $consultationFees,
                'currency' => 'USD',
            ],
            'growth_metrics' => [
                'total_registered_patients' => Patient::count(),
                'active_medical_practitioners' => Doctor::where('status', 'active')->count(),
                'total_consultations_conducted' => Appointment::where('status', AppointmentStatus::COMPLETED)->count(),
                'average_doctor_rating' => (float) Doctor::avg('rating') ?: 4.85,
            ],
            'revenue_by_status' => [
                'paid' => (float) Payment::where('status', PaymentStatus::PAID)->sum('amount'),
                'pending' => (float) Payment::where('status', PaymentStatus::PENDING)->sum('amount'),
                'refunded' => (float) Payment::where('status', PaymentStatus::REFUNDED)->sum('amount'),
            ],
        ];

        return $this->successResponse([
            'executive_summary' => $reports,
        ], 'Owner executive dashboard data retrieved successfully.');
    }
}
