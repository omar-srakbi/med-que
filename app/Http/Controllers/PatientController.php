<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('creator');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->latest()->paginate(15);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'national_id' => 'required|string|unique:patients,national_id',
            'phone' => 'required|string|max:20',
        ]);
        
        $validated['created_by'] = auth()->id();
        
        Patient::create($validated);
        
        return redirect()->route('patients.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة المريض بنجاح' : 'Patient added successfully');
    }

    public function show(Patient $patient)
    {
        $patient->load(['tickets.department', 'tickets.service', 'medicalRecords.department', 'medicalRecords.doctor']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
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
        
        return redirect()->route('patients.show', $patient)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات المريض بنجاح' : 'Patient updated successfully');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        
        return redirect()->route('patients.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف المريض بنجاح' : 'Patient deleted successfully');
    }
}
