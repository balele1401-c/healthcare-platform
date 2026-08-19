<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard Overview.
     */
    public function index(): View
    {
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        $activeDoctors = Doctor::where('is_available', true)->count();
        $appointmentsToday = Appointment::whereDate('appointment_date', today())->count();
        $pendingAppointments = Appointment::where('status', AppointmentStatus::PENDING)->count();
        $confirmedAppointments = Appointment::where('status', AppointmentStatus::CONFIRMED)->count();
        $completedAppointments = Appointment::where('status', AppointmentStatus::COMPLETED)->count();
        $cancelledAppointments = Appointment::where('status', AppointmentStatus::CANCELLED)->count();
        $totalRevenue = (float) Payment::where('payment_status', PaymentStatus::PAID)->sum('amount');
        $activePrescriptions = Prescription::where('status', PrescriptionStatus::ACTIVE)->count();

        // Appointment Status Distribution (for real chart visualization)
        $appointmentStats = [
            'pending' => $pendingAppointments,
            'confirmed' => $confirmedAppointments,
            'completed' => $completedAppointments,
            'cancelled' => $cancelledAppointments,
        ];

        // Recent Appointments
        $recentAppointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.specialty'])
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->take(6)
            ->get();

        // Recent Audit Logs
        $recentAuditLogs = AuditLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        // Recent Patients
        $recentPatients = Patient::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPatients',
            'totalDoctors',
            'activeDoctors',
            'appointmentsToday',
            'pendingAppointments',
            'confirmedAppointments',
            'completedAppointments',
            'cancelledAppointments',
            'totalRevenue',
            'activePrescriptions',
            'appointmentStats',
            'recentAppointments',
            'recentAuditLogs',
            'recentPatients'
        ));
    }
}
