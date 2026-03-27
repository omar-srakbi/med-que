<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Department;
use Illuminate\Http\Request;

class IncompletePatientController extends Controller
{
    public function index()
    {
        // Get all incomplete patients
        $patients = Patient::incomplete()
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        // Filter out patients who actually have complete info and auto-complete them
        foreach ($patients as $patient) {
            if ($patient->hasCompleteInfo()) {
                $patient->markComplete();
            }
        }

        // Reload the collection without auto-completed patients
        $patients = Patient::incomplete()
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('patients.incomplete', compact('patients'));
    }

    public function complete(Patient $patient)
    {
        // If patient already has complete info, redirect
        if ($patient->hasCompleteInfo()) {
            $patient->markComplete();
            return redirect()->route('patients.incomplete')
                ->with('success', app()->getLocale() === 'ar' ? 'ملف المريض مكتمل بالفعل' : 'Patient profile is already complete');
        }

        return view('patients.complete-profile', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'national_id' => 'required|string|unique:patients,national_id,' . $patient->id,
            'phone' => 'required|string|max:20',
        ]);

        $patient->update($validated);
        $patient->markComplete();

        return redirect()->route('patients.incomplete')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إكمال ملف المريض بنجاح' : 'Patient profile completed successfully');
    }
}
