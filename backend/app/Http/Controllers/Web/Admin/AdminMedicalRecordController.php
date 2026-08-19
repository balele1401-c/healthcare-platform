<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMedicalRecordController extends Controller
{
    /**
     * Display administrative overview of clinical medical records.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = MedicalRecord::with(['patient.user', 'doctor.user', 'doctor.specialty']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('record_number', 'ilike', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($dq) => $dq->where('name', 'ilike', "%{$search}%"))
                    ->orWhere('diagnosis', 'ilike', "%{$search}%");
            });
        }

        $medicalRecords = $query->latest('visit_date')->paginate(10)->withQueryString();

        return view('admin.medical_records.index', compact('medicalRecords', 'search'));
    }

    /**
     * Display administrative summary of a medical record.
     */
    public function show(MedicalRecord $medicalRecord): View
    {
        $medicalRecord->load(['patient.user', 'doctor.user', 'doctor.specialty', 'vitalSigns']);

        return view('admin.medical_records.show', compact('medicalRecord'));
    }
}
