<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorPatientController extends Controller
{
    /**
     * Display a listing of patients treated by or assigned to this doctor.
     */
    public function index(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $search = $request->query('search');

        $query = Patient::whereHas('appointments', function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })->with(['user']);

        if (! empty($search)) {
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        $patients = $query->paginate(10)->withQueryString();

        return view('doctor.patients.index', compact('patients', 'search'));
    }

    /**
     * Display clinical detail of an authorized patient.
     */
    public function show(Patient $patient): View
    {
        $doctor = Auth::user()->doctor;

        $hasClinicalRelationship = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->exists() || $patient->medicalRecords()
            ->where('doctor_id', $doctor->id)
            ->exists();

        if (! $hasClinicalRelationship) {
            abort(403, 'Access denied. You may only review clinical records for patients under your clinical consultation.');
        }

        $patient->load([
            'user',
            'appointments' => fn ($q) => $q->where('doctor_id', $doctor->id)->latest('appointment_date'),
            'medicalRecords' => fn ($q) => $q->where('doctor_id', $doctor->id)->latest('visit_date'),
            'prescriptions' => fn ($q) => $q->where('doctor_id', $doctor->id)->with('items.medicine')->latest('prescription_date'),
            'healthMetrics' => fn ($q) => $q->latest('measured_at')->take(10),
        ]);

        return view('doctor.patients.show', compact('patient'));
    }
}
