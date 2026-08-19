<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\View\View;

class AdminSpecialtyController extends Controller
{
    /**
     * Display medical specialties and doctor distribution.
     */
    public function index(): View
    {
        $specialties = Specialty::withCount('doctors')
            ->with(['doctors.user'])
            ->orderBy('name')
            ->get();

        return view('admin.specialties.index', compact('specialties'));
    }
}
