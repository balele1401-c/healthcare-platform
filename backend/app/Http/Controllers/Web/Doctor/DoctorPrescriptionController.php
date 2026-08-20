<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Enums\PrescriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorPrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions issued by this doctor.
     */
    public function index(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Prescription::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'items']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('prescription_code', 'like', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $prescriptions = $query->latest('prescription_date')->paginate(10)->withQueryString();

        return view('doctor.prescriptions.index', compact('prescriptions', 'search', 'status'));
    }

    /**
     * Show form to create a digital prescription.
     */
    public function create(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $patientId = $request->query('patient_id');

        $patients = Patient::whereHas('appointments', fn ($q) => $q->where('doctor_id', $doctor->id))
            ->with('user')
            ->get();

        return view('doctor.prescriptions.create', compact('patients', 'patientId'));
    }

    /**
     * Store new electronic prescription.
     */
    public function store(Request $request): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'prescription_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_name' => ['required', 'string', 'max:255'],
            'items.*.dosage' => ['required', 'string', 'max:100'],
            'items.*.dosage_form' => ['required', 'string', 'max:100'],
            'items.*.frequency' => ['required', 'string', 'max:100'],
            'items.*.duration' => ['required', 'string', 'max:100'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.refills_available' => ['nullable', 'integer', 'min:0'],
            'items.*.instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $prescription = DB::transaction(function () use ($validated, $doctor, $request) {
            $rx = Prescription::create([
                'prescription_code' => 'RX-' . strtoupper(Str::random(8)),
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $doctor->id,
                'prescription_date' => $validated['prescription_date'],
                'status' => PrescriptionStatus::ACTIVE,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $medicine = \App\Models\Medicine::firstOrCreate(
                    ['name' => $item['medicine_name']],
                    [
                        'dosage_form' => $item['dosage_form'] ?? 'Tablet',
                        'strength' => $item['dosage'] ?? 'Standard',
                        'status' => 'active',
                    ]
                );

                PrescriptionItem::create([
                    'prescription_id' => $rx->id,
                    'medicine_id' => $medicine->id,
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                    'refills_available' => $item['refills_available'] ?? 0,
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'DOCTOR_CREATE_PRESCRIPTION',
                'entity_type' => 'Prescription',
                'entity_id' => $rx->id,
                'new_data' => ['prescription_code' => $rx->prescription_code, 'patient_id' => $rx->patient_id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $rx;
        });

        return redirect()->route('doctor.prescriptions.show', $prescription->id)
            ->with('success', 'Prescription #' . $prescription->prescription_code . ' created successfully.');
    }

    /**
     * Display prescription details.
     */
    public function show(Prescription $prescription): View
    {
        $doctor = Auth::user()->doctor;

        if ($prescription->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You may only review prescriptions issued by your practice.');
        }

        $prescription->load(['patient.user', 'items.medicine']);

        return view('doctor.prescriptions.show', compact('prescription'));
    }
}
