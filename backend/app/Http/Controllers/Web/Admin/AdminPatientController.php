<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    /**
     * Display a paginated listing of patients.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $bloodType = $request->query('blood_type');

        $query = Patient::with(['user']);

        if (! empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (! empty($bloodType)) {
            $query->where('blood_type', $bloodType);
        }

        $patients = $query->latest()->paginate(10)->withQueryString();

        return view('admin.patients.index', compact('patients', 'search', 'bloodType'));
    }

    /**
     * Display patient details and demographic history.
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'user',
            'appointments.doctor.user',
            'appointments.doctor.specialty',
            'healthMetrics' => fn ($q) => $q->latest('recorded_at')->take(10),
            'prescriptions' => fn ($q) => $q->latest('prescription_date')->take(5),
        ]);

        return view('admin.patients.show', compact('patient'));
    }
}
