<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'department', 'doctor', 'ticket']);
        
        if ($request->has('patient')) {
            $query->where('patient_id', $request->patient);
        }
        
        if ($request->has('department')) {
            $query->where('department_id', $request->department);
        }
        
        $records = $query->latest()->paginate(15);
        $patients = Patient::latest()->limit(100)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view('medical-records.index', compact('records', 'patients', 'departments'));
    }

    public function create()
    {
        $patients = Patient::latest()->limit(100)->get();
        $departments = Department::where('is_active', true)->get();
        $todayTickets = Ticket::whereDate('visit_date', today())
            ->whereNull('completed_at')
            ->get();
        
        return view('medical-records.create', compact('patients', 'departments', 'todayTickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'diagnosis' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'notes' => 'nullable|string',
            'test_results' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
        ]);
        
        $validated['doctor_id'] = auth()->id();
        
        MedicalRecord::create($validated);
        
        // Mark ticket as completed if provided
        if ($request->ticket_id) {
            Ticket::findOrFail($request->ticket_id)->update(['completed_at' => now()]);
        }
        
        return redirect()->route('medical-records.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة السجل الطبي بنجاح' : 'Medical record added successfully');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'department', 'doctor', 'ticket', 'updater']);
        return view('medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $patients = Patient::latest()->limit(100)->get();
        $departments = Department::where('is_active', true)->get();
        
        return view('medical-records.edit', compact('medicalRecord', 'patients', 'departments'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'diagnosis' => 'nullable|string',
            'prescriptions' => 'nullable|string',
            'notes' => 'nullable|string',
            'test_results' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        // Track who edited this record
        $validated['updated_by'] = auth()->id();

        $medicalRecord->update($validated);

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث السجل الطبي بنجاح' : 'Medical record updated successfully');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        
        return redirect()->route('medical-records.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف السجل الطبي بنجاح' : 'Medical record deleted successfully');
    }
}
