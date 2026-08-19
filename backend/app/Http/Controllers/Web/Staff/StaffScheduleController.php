<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffScheduleController extends Controller
{
    /**
     * Display weekly consultation shifts matrix across all doctors.
     */
    public function index(Request $request): View
    {
        $doctorId = $request->query('doctor_id');
        $dayOfWeek = $request->query('day_of_week');

        $query = DoctorSchedule::with(['doctor.user', 'doctor.specialty']);

        if (! empty($doctorId)) {
            $query->where('doctor_id', $doctorId);
        }

        if (! empty($dayOfWeek)) {
            $query->where('day_of_week', $dayOfWeek);
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->paginate(15)->withQueryString();
        $doctors = Doctor::with('user')->where('status', 'active')->get();

        return view('staff.schedules.index', compact('schedules', 'doctors', 'doctorId', 'dayOfWeek'));
    }
}
