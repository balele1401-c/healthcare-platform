<?php

namespace App\Http\Controllers\Web\Staff;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffPatientController extends Controller
{
    /**
     * Display a listing of registered patients.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Patient::with(['user']);

        if (! empty($search)) {
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(10)->withQueryString();

        return view('staff.patients.index', compact('patients', 'search'));
    }

    /**
     * Show form for staff-assisted patient registration.
     */
    public function create(): View
    {
        return view('staff.patients.create');
    }

    /**
     * Store a newly registered patient account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
            'blood_type' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $patient = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => UserRole::PATIENT,
                'status' => UserStatus::ACTIVE,
            ]);

            $pat = Patient::create([
                'user_id' => $user->id,
                'blood_type' => $validated['blood_type'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'STAFF_REGISTER_PATIENT',
                'entity_type' => 'Patient',
                'entity_id' => $pat->id,
                'new_data' => ['email' => $user->email, 'patient_id' => $pat->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $pat;
        });

        return redirect()->route('staff.patients.show', $patient->id)
            ->with('success', 'Patient account for ' . $validated['name'] . ' registered successfully.');
    }

    /**
     * Display patient operational profile (non-clinical).
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'user',
            'appointments' => fn ($q) => $q->with(['doctor.user', 'doctor.specialty'])->latest('appointment_date'),
        ]);

        return view('staff.patients.show', compact('patient'));
    }
}
