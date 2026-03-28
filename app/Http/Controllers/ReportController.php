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

    public function monthlyRevenue(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $previousYear = $year - 1;

        $dbDriver = \DB::connection()->getDriverName();

        // Monthly revenue for selected year (SQLite compatible)
        if ($dbDriver === 'sqlite') {
            $monthlyRevenue = Payment::selectRaw('
                    CAST(strftime("%m", created_at) AS INTEGER) as month,
                    SUM(amount) as total,
                    COUNT(*) as transactions
                ')
                ->whereRaw("strftime('%Y', created_at) = ?", [$year])
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Monthly revenue for previous year (for comparison)
            $previousYearRevenue = Payment::selectRaw('
                    CAST(strftime("%m", created_at) AS INTEGER) as month,
                    SUM(amount) as total
                ')
                ->whereRaw("strftime('%Y', created_at) = ?", [$previousYear])
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            // Revenue by payment method
            $revenueByMethod = Payment::selectRaw('
                    payment_method,
                    SUM(amount) as total,
                    COUNT(*) as count
                ')
                ->whereRaw("strftime('%Y', created_at) = ?", [$year])
                ->groupBy('payment_method')
                ->get();
        } else {
            // MySQL/MariaDB
            $monthlyRevenue = Payment::selectRaw('
                    MONTH(created_at) as month,
                    SUM(amount) as total,
                    COUNT(*) as transactions
                ')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $previousYearRevenue = Payment::selectRaw('
                    MONTH(created_at) as month,
                    SUM(amount) as total
                ')
                ->whereYear('created_at', $previousYear)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $revenueByMethod = Payment::selectRaw('
                    payment_method,
                    SUM(amount) as total,
                    COUNT(*) as count
                ')
                ->whereYear('created_at', $year)
                ->groupBy('payment_method')
                ->get();
        }

        // Calculate totals (SQLite compatible)
        if ($dbDriver === 'sqlite') {
            $totalRevenue = Payment::whereRaw("strftime('%Y', created_at) = ?", [$year])->sum('amount');
            $previousYearTotal = Payment::whereRaw("strftime('%Y', created_at) = ?", [$previousYear])->sum('amount');
        } else {
            $totalRevenue = Payment::whereYear('created_at', $year)->sum('amount');
            $previousYearTotal = Payment::whereYear('created_at', $previousYear)->sum('amount');
        }
        
        $growthPercentage = $previousYearTotal > 0 ? (($totalRevenue - $previousYearTotal) / $previousYearTotal) * 100 : 0;

        // Monthly data for chart
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $current = $monthlyRevenue[$i]->total ?? 0;
            $previous = $previousYearRevenue[$i]->total ?? 0;
            $growth = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
            
            $chartData[] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'month_ar' => date('F', mktime(0, 0, 0, $i, 1)),
                'current' => $current,
                'previous' => $previous,
                'growth' => $growth,
            ];
        }

        return view('reports.monthly-revenue', compact(
            'year',
            'chartData',
            'totalRevenue',
            'previousYearTotal',
            'growthPercentage',
            'monthlyRevenue',
            'revenueByMethod'
        ));
    }

    public function departmentPerformance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $departmentPerformance = Ticket::join('departments', 'tickets.department_id', '=', 'departments.id')
            ->join('services', 'tickets.service_id', '=', 'services.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                departments.id,
                departments.name,
                departments.name_ar,
                COUNT(tickets.id) as visits,
                SUM(tickets.amount_paid) as revenue
            ')
            ->groupBy('departments.id', 'departments.name', 'departments.name_ar')
            ->orderBy('visits', 'desc')
            ->get();

        $totalVisits = $departmentPerformance->sum('visits');
        $totalRevenue = $departmentPerformance->sum('revenue');

        return view('reports.department-performance', compact(
            'departmentPerformance',
            'totalVisits',
            'totalRevenue',
            'startDate',
            'endDate'
        ));
    }

    public function cashierPerformance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $cashierPerformance = Ticket::join('users', 'tickets.cashier_id', '=', 'users.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                users.id,
                users.full_name,
                COUNT(tickets.id) as transactions,
                SUM(tickets.amount_paid) as revenue,
                AVG(tickets.amount_paid) as avg_transaction
            ')
            ->groupBy('users.id', 'users.full_name')
            ->orderBy('revenue', 'desc')
            ->get();

        $totalTransactions = $cashierPerformance->sum('transactions');
        $totalRevenue = $cashierPerformance->sum('revenue');

        return view('reports.cashier-performance', compact(
            'cashierPerformance',
            'totalTransactions',
            'totalRevenue',
            'startDate',
            'endDate'
        ));
    }

    public function services(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $servicesPerformance = Ticket::join('services', 'tickets.service_id', '=', 'services.id')
            ->join('departments', 'tickets.department_id', '=', 'departments.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                services.id,
                services.name,
                services.name_ar,
                services.price,
                COUNT(tickets.id) as count,
                SUM(tickets.amount_paid) as total_revenue
            ')
            ->groupBy('services.id', 'services.name', 'services.name_ar', 'services.price')
            ->orderBy('count', 'desc')
            ->get();

        $totalServices = $servicesPerformance->sum('count');
        $totalRevenue = $servicesPerformance->sum('total_revenue');

        return view('reports.services', compact(
            'servicesPerformance',
            'totalServices',
            'totalRevenue',
            'startDate',
            'endDate'
        ));
    }

    public function patientDemographics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear());
        $endDate = $request->input('end_date', now()->endOfYear());

        // Age distribution
        $ageGroups = Patient::selectRaw('
                CASE
                    WHEN birth_date IS NULL THEN "Unknown"
                    WHEN strftime("%Y", "now") - strftime("%Y", birth_date) < 18 THEN "0-17"
                    WHEN strftime("%Y", "now") - strftime("%Y", birth_date) BETWEEN 18 AND 30 THEN "18-30"
                    WHEN strftime("%Y", "now") - strftime("%Y", birth_date) BETWEEN 31 AND 45 THEN "31-45"
                    WHEN strftime("%Y", "now") - strftime("%Y", birth_date) BETWEEN 46 AND 60 THEN "46-60"
                    ELSE "60+"
                END as age_group,
                COUNT(*) as count
            ')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('age_group')
            ->get();

        // Geographic distribution
        $geoDistribution = Patient::selectRaw('birth_place, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('birth_place')
            ->groupBy('birth_place')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $totalPatients = Patient::whereBetween('created_at', [$startDate, $endDate])->count();

        return view('reports.patient-demographics', compact(
            'ageGroups',
            'geoDistribution',
            'totalPatients',
            'startDate',
            'endDate'
        ));
    }

    public function visitFrequency(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear());
        $endDate = $request->input('end_date', now()->endOfYear());

        // Patients with multiple visits
        $frequentVisitors = Ticket::selectRaw('
                patient_id,
                COUNT(*) as visit_count,
                SUM(amount_paid) as total_spent
            ')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('visit_count', 'desc')
            ->limit(50)
            ->get();

        // Load patient details
        $frequentVisitors->load(['patient' => function($q) {
            $q->select('id', 'first_name', 'last_name', 'national_id', 'phone');
        }]);

        $totalPatients = Ticket::whereBetween('visit_date', [$startDate, $endDate])
            ->distinct('patient_id')
            ->count('patient_id');

        $returningPatients = $frequentVisitors->count();
        $returningRate = $totalPatients > 0 ? ($returningPatients / $totalPatients) * 100 : 0;

        $avgVisits = Ticket::whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('patient_id')
            ->get()
            ->avg(function($t) use ($startDate, $endDate) {
                return Ticket::where('patient_id', $t->patient_id)
                    ->whereBetween('visit_date', [$startDate, $endDate])
                    ->count();
            });

        return view('reports.visit-frequency', compact(
            'frequentVisitors',
            'totalPatients',
            'returningPatients',
            'returningRate',
            'startDate',
            'endDate'
        ));
    }
}
