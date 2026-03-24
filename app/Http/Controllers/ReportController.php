<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function dailyPatients(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));
        
        $patients = Patient::whereDate('created_at', $date)->get();
        $totalPatients = $patients->count();
        
        // Patients by department visits
        $departmentVisits = Ticket::whereDate('visit_date', $date)
            ->select('department_id', DB::raw('count(*) as count'))
            ->groupBy('department_id')
            ->with('department')
            ->get();
        
        return view('reports.daily-patients', compact('date', 'patients', 'totalPatients', 'departmentVisits'));
    }

    public function dailyRevenue(Request $request)
    {
        $date = $request->input('date', today()->format('Y-m-d'));

        $payments = Payment::whereDate('created_at', $date)
            ->with(['ticket.department', 'cashier'])
            ->latest()
            ->get();

        $totalRevenue = $payments->sum('amount');
        $totalTransactions = $payments->count();

        // Revenue by department
        $revenueByDept = Payment::join('tickets', 'payments.ticket_id', '=', 'tickets.id')
            ->join('departments', 'tickets.department_id', '=', 'departments.id')
            ->whereDate('payments.created_at', $date)
            ->select('tickets.department_id', DB::raw('sum(payments.amount) as total'))
            ->groupBy('tickets.department_id')
            ->get()
            ->map(function($item) {
                $item->department = Department::find($item->department_id);
                return $item;
            });

        return view('reports.daily-revenue', compact('date', 'payments', 'totalRevenue', 'totalTransactions', 'revenueByDept'));
    }

    public function patientHistory(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patient = null;
        $tickets = null;
        $medicalRecords = null;
        
        if ($patientId) {
            $patient = Patient::with('creator')->findOrFail($patientId);
            $tickets = Ticket::where('patient_id', $patientId)
                ->with(['department', 'service'])
                ->latest()
                ->get();
            $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $patientId)
                ->with(['department', 'doctor'])
                ->latest()
                ->get();
        }
        
        $patients = Patient::latest()->limit(100)->get();
        
        return view('reports.patient-history', compact('patients', 'patient', 'tickets', 'medicalRecords'));
    }
}
