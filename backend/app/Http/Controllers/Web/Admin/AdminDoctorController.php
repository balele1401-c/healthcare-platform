<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    /**
     * Display a paginated listing of doctors.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $specialtyId = $request->query('specialty_id');
        $status = $request->query('status');

        $query = Doctor::with(['user', 'specialty', 'schedules']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('facility', 'like', "%{$search}%");
            });
        }

        if (! empty($specialtyId)) {
            $query->where('specialty_id', $specialtyId);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_available', $status === 'available');
        }

        $doctors = $query->paginate(10)->withQueryString();
        $specialties = Specialty::orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'specialties', 'search', 'specialtyId', 'status'));
    }

    /**
     * Display doctor profile, weekly consultation hours, and clinical activities.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load([
            'user',
            'specialty',
            'schedules',
            'appointments.patient.user' => fn ($q) => $q->latest()->take(10),
        ]);

        return view('admin.doctors.show', compact('doctor'));
    }
}
