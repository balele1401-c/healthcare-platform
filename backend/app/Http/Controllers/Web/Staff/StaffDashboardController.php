<?php

namespace App\Http\Controllers\Web\Staff;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    /**
     * Display the Operational Staff Dashboard Overview.
     */
    public function index(): View
    {
        $staff = Auth::user()->staff;
        $today = today()->toDateString();

        $todayAppointmentsCount = Appointment::whereDate('appointment_date', $today)->count();
        $pendingAppointmentsCount = Appointment::where('status', AppointmentStatus::PENDING)->count();
        $confirmedAppointmentsCount = Appointment::where('status', AppointmentStatus::CONFIRMED)->count();
        $activeDoctorsCount = Doctor::where('status', 'active')->count();
        $registeredPatientsCount = Patient::count();
        $unpaidAppointmentsCount = Payment::where('status', \App\Enums\PaymentStatus::PENDING)->count();

        // Recent Appointments for Operational Monitoring
        $recentAppointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.specialty', 'payment'])
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->take(8)
            ->get();

        // Recent Operational Activity
        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('staff.dashboard', compact(
            'staff',
            'todayAppointmentsCount',
            'pendingAppointmentsCount',
            'confirmedAppointmentsCount',
            'activeDoctorsCount',
            'registeredPatientsCount',
            'unpaidAppointmentsCount',
            'recentAppointments',
            'recentActivities'
        ));
    }
}
