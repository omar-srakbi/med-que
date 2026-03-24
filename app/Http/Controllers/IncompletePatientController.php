<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Department;
use Illuminate\Http\Request;

class IncompletePatientController extends Controller
{
    public function index()
    {
        $patients = Patient::incomplete()
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        
        return view('patients.incomplete', compact('patients'));
    }

    public function complete(Patient $patient)
    {
        return view('patients.complete-profile', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
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
