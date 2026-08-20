<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\HealthMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorHealthMetricController extends Controller
{
    /**
     * Display health metrics logged by patients under this doctor's care.
     */
    public function index(Request $request): View
    {
        $doctor = Auth::user()->doctor;
        $search = $request->query('search');
        $metricType = $request->query('metric_type');

        $query = HealthMetric::whereHas('patient.appointments', function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })->with(['patient.user']);

        if (! empty($search)) {
            $query->whereHas('patient.user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%");
            });
        }

        if (! empty($metricType)) {
            $query->where('metric_type', $metricType);
        }

        $metrics = $query->latest('measured_at')->paginate(15)->withQueryString();

        return view('doctor.health_metrics.index', compact('metrics', 'search', 'metricType'));
    }
}
