<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\VitalSign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorMedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records authored by this doctor.
     */
    public function index(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $search = $request->query('search');

        $query = MedicalRecord::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'vitalSigns']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('record_number', 'ilike', "%{$search}%")
                    ->orWhere('diagnosis', 'ilike', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $medicalRecords = $query->latest('visit_date')->paginate(10)->withQueryString();

        return view('doctor.medical_records.index', compact('medicalRecords', 'search'));
    }

    /**
     * Show form to create an electronic medical record.
     */
    public function create(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $patientId = $request->query('patient_id');

        $patients = Patient::whereHas('appointments', fn ($q) => $q->where('doctor_id', $doctor->id))
            ->with('user')
            ->get();

        return view('doctor.medical_records.create', compact('patients', 'patientId'));
    }

    /**
     * Store new electronic medical record.
     */
    public function store(Request $request): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'visit_date' => ['required', 'date'],
            'chief_complaint' => ['required', 'string', 'max:500'],
            'symptoms' => ['nullable', 'string', 'max:1000'],
            'diagnosis' => ['required', 'string', 'max:500'],
            'treatment' => ['nullable', 'string', 'max:1000'],
            'follow_up_date' => ['nullable', 'date'],
            'clinical_notes' => ['nullable', 'string'],
            'systolic' => ['nullable', 'integer', 'min:50', 'max:250'],
            'diastolic' => ['nullable', 'integer', 'min:30', 'max:150'],
            'heart_rate' => ['nullable', 'integer', 'min:30', 'max:220'],
            'body_temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'blood_oxygen' => ['nullable', 'integer', 'min:50', 'max:100'],
        ]);

        $record = DB::transaction(function () use ($validated, $doctor, $request) {
            $rec = MedicalRecord::create([
                'record_number' => 'REC-' . strtoupper(Str::random(8)),
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $doctor->id,
                'visit_date' => $validated['visit_date'],
                'chief_complaint' => $validated['chief_complaint'],
                'symptoms' => $validated['symptoms'] ?? null,
                'diagnosis' => $validated['diagnosis'],
                'treatment' => $validated['treatment'] ?? null,
                'follow_up_date' => $validated['follow_up_date'] ?? null,
                'clinical_notes' => $validated['clinical_notes'] ?? null,
                'facility' => $doctor->facility ?? 'Metropolitan Medical Center',
            ]);

            if (! empty($validated['systolic']) || ! empty($validated['heart_rate'])) {
                VitalSign::create([
                    'medical_record_id' => $rec->id,
                    'systolic_blood_pressure' => $validated['systolic'] ?? null,
                    'diastolic_blood_pressure' => $validated['diastolic'] ?? null,
                    'heart_rate' => $validated['heart_rate'] ?? null,
                    'body_temperature' => $validated['body_temperature'] ?? null,
                    'blood_oxygen' => $validated['blood_oxygen'] ?? null,
                    'measured_at' => now(),
                ]);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'DOCTOR_CREATE_MEDICAL_RECORD',
                'entity_type' => 'MedicalRecord',
                'entity_id' => $rec->id,
                'new_data' => ['record_number' => $rec->record_number, 'patient_id' => $rec->patient_id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $rec;
        });

        return redirect()->route('doctor.medical-records.show', $record->id)
            ->with('success', 'Medical Record #' . $record->record_number . ' documented successfully.');
    }

    /**
     * Display medical record details.
     */
    public function show(MedicalRecord $medicalRecord): View
    {
        $doctor = Auth::user()->doctor;

        if ($medicalRecord->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You may only review clinical records created under your practice.');
        }

        $medicalRecord->load(['patient.user', 'vitalSigns', 'prescriptions.items']);

        return view('doctor.medical_records.show', compact('medicalRecord'));
    }
}
