<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffDoctorController extends Controller
{
    /**
     * Display doctors directory for operational scheduling.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $specialtyId = $request->query('specialty_id');
        $status = $request->query('status');

        $query = Doctor::with(['user', 'specialty']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('facility', 'ilike', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if (! empty($specialtyId)) {
            $query->where('specialty_id', $specialtyId);
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $doctors = $query->paginate(10)->withQueryString();
        $specialties = Specialty::orderBy('name')->get();

        return view('staff.doctors.index', compact('doctors', 'specialties', 'search', 'specialtyId', 'status'));
    }

    /**
     * Display doctor practitioner details & schedules.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load(['user', 'specialty', 'schedules' => fn ($q) => $q->orderBy('day_of_week')]);

        return view('staff.doctors.show', compact('doctor'));
    }
}
