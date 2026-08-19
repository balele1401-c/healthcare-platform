<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorDashboardController extends Controller
{
    /**
     * Display the Doctor Consultation Cockpit Overview.
     */
    public function index(): View
    {
        $doctor = Auth::user()->doctor;

        $today = today()->toDateString();

        $todayAppointmentsCount = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->count();

        $upcomingAppointmentsCount = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', '>=', $today)
            ->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED])
            ->count();

        $pendingAppointmentsCount = Appointment::where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::PENDING)
            ->count();

        $completedConsultationsCount = Appointment::where('doctor_id', $doctor->id)
            ->where('status', AppointmentStatus::COMPLETED)
            ->count();

        $totalPatientsCount = Appointment::where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        $activePrescriptionsCount = Prescription::where('doctor_id', $doctor->id)
            ->where('status', 'active')
            ->count();

        // Today's Appointments List
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->with(['patient.user'])
            ->orderBy('appointment_time')
            ->get();

        // Upcoming Appointments List (next 5)
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', '>=', $today)
            ->with(['patient.user'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Recent Medical Records
        $recentRecords = MedicalRecord::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->latest('visit_date')
            ->take(5)
            ->get();

        // Active Conversations
        $conversations = ChatConversation::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'messages' => fn ($q) => $q->latest()->take(1)])
            ->take(4)
            ->get();

        return view('doctor.dashboard', compact(
            'doctor',
            'todayAppointmentsCount',
            'upcomingAppointmentsCount',
            'pendingAppointmentsCount',
            'completedConsultationsCount',
            'totalPatientsCount',
            'activePrescriptionsCount',
            'todayAppointments',
            'upcomingAppointments',
            'recentRecords',
            'conversations'
        ));
    }
}
