<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericReportExport;

class ReportExportController extends Controller
{
    public function exportPdf($type, Request $request)
    {
        $data = $this->getReportData($type, $request);
        
        $pdf = Pdf::loadView('reports.exports.pdf-template', $data);
        
        return $pdf->download($this->getFilename($type) . '.pdf');
    }

    public function exportExcel($type, Request $request)
    {
        $data = $this->getReportData($type, $request);
        
        return Excel::download(new GenericReportExport($data, $type), $this->getFilename($type) . '.xlsx');
    }

    public function exportCsv($type, Request $request)
    {
        $data = $this->getReportData($type, $request);
        
        return Excel::download(new GenericReportExport($data, $type, 'csv'), $this->getFilename($type) . '.csv');
    }

    private function getReportData($type, $request)
    {
        switch ($type) {
            case 'monthly-revenue':
                return $this->getMonthlyRevenueData($request);
            
            case 'department-performance':
                return $this->getDepartmentPerformanceData($request);
            
            case 'cashier-performance':
                return $this->getCashierPerformanceData($request);
            
            case 'services':
                return $this->getServicesData($request);
            
            case 'patient-demographics':
                return $this->getPatientDemographicsData($request);
            
            case 'visit-frequency':
                return $this->getVisitFrequencyData($request);
            
            default:
                return [];
        }
    }

    private function getMonthlyRevenueData($request)
    {
        $year = $request->input('year', date('Y'));
        $previousYear = $year - 1;

        $dbDriver = DB::connection()->getDriverName();

        if ($dbDriver === 'sqlite') {
            $monthlyRevenue = DB::table('payments')
                ->selectRaw('CAST(strftime("%m", created_at) AS INTEGER) as month, SUM(amount) as total, COUNT(*) as transactions')
                ->whereRaw("strftime('%Y', created_at) = ?", [$year])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthlyRevenue = DB::table('payments')
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as transactions')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        $totalRevenue = DB::table('payments')
            ->whereRaw($dbDriver === 'sqlite' ? "strftime('%Y', created_at) = ?" : 'YEAR(created_at) = ?', [$year])
            ->sum('amount');

        return [
            'title' => app()->getLocale() === 'ar' ? 'تقرير الإيرادات الشهري' : 'Monthly Revenue Report',
            'year' => $year,
            'monthlyRevenue' => $monthlyRevenue,
            'totalRevenue' => $totalRevenue,
            'generated_at' => now(),
        ];
    }

    private function getDepartmentPerformanceData($request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('tickets')
            ->join('departments', 'tickets.department_id', '=', 'departments.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                departments.name,
                departments.name_ar,
                COUNT(tickets.id) as visits,
                SUM(tickets.amount_paid) as revenue
            ')
            ->groupBy('departments.id', 'departments.name', 'departments.name_ar')
            ->orderBy('visits', 'desc')
            ->get();

        return [
            'title' => app()->getLocale() === 'ar' ? 'أداء الأقسام' : 'Department Performance',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
            'generated_at' => now(),
        ];
    }

    private function getCashierPerformanceData($request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('tickets')
            ->join('users', 'tickets.cashier_id', '=', 'users.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                users.full_name,
                COUNT(tickets.id) as transactions,
                SUM(tickets.amount_paid) as revenue
            ')
            ->groupBy('users.id', 'users.full_name')
            ->orderBy('revenue', 'desc')
            ->get();

        return [
            'title' => app()->getLocale() === 'ar' ? 'أداء الأمناء' : 'Cashier Performance',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
            'generated_at' => now(),
        ];
    }

    private function getServicesData($request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('tickets')
            ->join('services', 'tickets.service_id', '=', 'services.id')
            ->whereBetween('tickets.visit_date', [$startDate, $endDate])
            ->selectRaw('
                services.name,
                services.name_ar,
                services.price,
                COUNT(tickets.id) as count,
                SUM(tickets.amount_paid) as total_revenue
            ')
            ->groupBy('services.id', 'services.name', 'services.name_ar', 'services.price')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'title' => app()->getLocale() === 'ar' ? 'أداء الخدمات' : 'Services Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
            'generated_at' => now(),
        ];
    }

    private function getPatientDemographicsData($request)
    {
        $startDate = $request->input('start_date', now()->startOfYear());
        $endDate = $request->input('end_date', now()->endOfYear());

        $dbDriver = DB::connection()->getDriverName();

        if ($dbDriver === 'sqlite') {
            $ageGroups = DB::table('patients')
                ->selectRaw('
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
        }

        return [
            'title' => app()->getLocale() === 'ar' ? 'التركيبة السكانية' : 'Patient Demographics',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $ageGroups ?? [],
            'generated_at' => now(),
        ];
    }

    private function getVisitFrequencyData($request)
    {
        $startDate = $request->input('start_date', now()->startOfYear());
        $endDate = $request->input('end_date', now()->endOfYear());

        $data = DB::table('tickets')
            ->selectRaw('patient_id, COUNT(*) as visit_count, SUM(amount_paid) as total_spent')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('visit_count', 'desc')
            ->limit(50)
            ->get();

        return [
            'title' => app()->getLocale() === 'ar' ? 'تكرار الزيارات' : 'Visit Frequency',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
            'generated_at' => now(),
        ];
    }

    private function getFilename($type)
    {
        $prefix = app()->getLocale() === 'ar' ? 'تقرير' : 'Report';
        $date = now()->format('Y-m-d');
        
        $names = [
            'monthly-revenue' => 'Monthly_Revenue',
            'department-performance' => 'Department_Performance',
            'cashier-performance' => 'Cashier_Performance',
            'services' => 'Services',
            'patient-demographics' => 'Patient_Demographics',
            'visit-frequency' => 'Visit_Frequency',
        ];

        return "{$prefix}_{$names[$type]}_{$date}";
    }
}
