<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Ticket;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'patients' => [],
                'tickets' => [],
                'medical_records' => [],
                'staff' => []
            ]);
        }
        
        // Search patients
        $patients = Patient::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('national_id', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->full_name,
                    'national_id' => $p->national_id,
                    'url' => route('patients.show', $p),
                    'type' => app()->getLocale() === 'ar' ? 'مريض' : 'Patient'
                ];
            });
        
        // Search tickets
        $tickets = Ticket::with(['patient', 'department'])
            ->where('ticket_number', 'like', "%{$query}%")
            ->orWhereHas('patient', function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('national_id', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'ticket_number' => $t->ticket_number,
                    'patient' => $t->patient->full_name,
                    'department' => app()->getLocale() === 'ar' ? $t->department->name_ar : $t->department->name,
                    'url' => route('tickets.show', $t),
                    'type' => app()->getLocale() === 'ar' ? 'تذكرة' : 'Ticket'
                ];
            });
        
        // Search medical records
        $medicalRecords = MedicalRecord::with(['patient', 'doctor'])
            ->whereHas('patient', function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('national_id', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'patient' => $r->patient->full_name,
                    'doctor' => $r->doctor->full_name,
                    'diagnosis' => Str::limit($r->diagnosis, 30),
                    'url' => route('medical-records.show', $r),
                    'type' => app()->getLocale() === 'ar' ? 'سجل طبي' : 'Medical Record'
                ];
            });
        
        // Search staff (admin only)
        $staff = [];
        if (auth()->user()->role->name === 'Admin') {
            $staff = User::with('role')
                ->where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->full_name,
                        'email' => $u->email,
                        'role' => app()->getLocale() === 'ar' ? $u->role->name_ar : $u->role->name,
                        'url' => route('staff.show', $u),
                        'type' => app()->getLocale() === 'ar' ? 'موظف' : 'Staff'
                    ];
                });
        }
        
        return response()->json([
            'patients' => $patients,
            'tickets' => $tickets,
            'medical_records' => $medicalRecords,
            'staff' => $staff
        ]);
    }
}
