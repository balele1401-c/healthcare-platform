<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Prescription::with(['patient.user', 'doctor.user', 'doctor.specialty', 'items']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('prescription_code', 'ilike', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($pq) => $pq->where('name', 'ilike', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($dq) => $dq->where('name', 'ilike', "%{$search}%"));
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        $prescriptions = $query->latest('prescription_date')->paginate(10)->withQueryString();

        return view('admin.prescriptions.index', compact('prescriptions', 'search', 'status'));
    }

    /**
     * Display prescription details.
     */
    public function show(Prescription $prescription): View
    {
        $prescription->load(['patient.user', 'doctor.user', 'doctor.specialty', 'items']);

        return view('admin.prescriptions.show', compact('prescription'));
    }
}
