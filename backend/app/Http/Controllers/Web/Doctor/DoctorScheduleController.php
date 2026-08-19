<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Enums\ConsultationType;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DoctorSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /**
     * Display weekly consultation schedule slots.
     */
    public function index(): View
    {
        $doctor = Auth::user()->doctor;

        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('doctor.schedules.index', compact('schedules', 'doctor'));
    }

    /**
     * Store new consultation schedule slot.
     */
    public function store(Request $request): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'consultation_type' => ['required', 'string', 'in:in_person,online'],
            'slot_duration_minutes' => ['required', 'integer', 'min:10', 'max:120'],
            'max_patients' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        // Check for time slot overlap on the same day
        $overlap = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($sub) use ($validated) {
                        $sub->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'This consultation slot overlaps with an existing schedule on that day.'])
                ->withInput();
        }

        $schedule = DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'consultation_type' => ConsultationType::from($validated['consultation_type']),
            'facility' => $doctor->facility ?? 'Metropolitan Medical Center',
            'slot_duration_minutes' => $validated['slot_duration_minutes'],
            'max_patients' => $validated['max_patients'] ?? 10,
            'is_available' => true,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DOCTOR_ADD_SCHEDULE_SLOT',
            'entity_type' => 'DoctorSchedule',
            'entity_id' => $schedule->id,
            'new_data' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Consultation schedule slot created successfully.');
    }

    /**
     * Toggle availability status of a schedule slot.
     */
    public function toggle(DoctorSchedule $schedule): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        if ($schedule->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You can only modify your own consultation schedules.');
        }

        $schedule->update(['is_available' => ! $schedule->is_available]);

        return back()->with('success', 'Schedule slot status updated.');
    }
}
