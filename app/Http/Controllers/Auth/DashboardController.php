<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Department;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Current stats
        $stats = [
            'total_patients' => Patient::count(),
            'today_patients' => Patient::whereDate('created_at', $today)->count(),
            'today_tickets' => Ticket::whereDate('visit_date', $today)->count(),
            'today_revenue' => Payment::whereDate('created_at', $today)->sum('amount'),
            'pending_tickets' => Ticket::whereDate('visit_date', $today)
                ->whereNull('completed_at')
                ->count(),
        ];

        // Yesterday's stats for comparison
        $yesterdayStats = [
            'patients' => Patient::whereDate('created_at', $yesterday)->count(),
            'tickets' => Ticket::whereDate('visit_date', $yesterday)->count(),
            'revenue' => Payment::whereDate('created_at', $yesterday)->sum('amount'),
        ];

        // Calculate trends
        $stats['patients_trend'] = $this->calculateTrend($stats['today_patients'], $yesterdayStats['patients']);
        $stats['tickets_trend'] = $this->calculateTrend($stats['today_tickets'], $yesterdayStats['tickets']);
        $stats['revenue_trend'] = $this->calculateTrend($stats['today_revenue'], $yesterdayStats['revenue']);

        // Chart data
        $patientsTrend = $this->getPatientsTrend(7);
        $revenueTrend = $this->getRevenueTrend(7);
        $departmentDistribution = $this->getDepartmentDistribution();
        $ticketStatus = $this->getTicketStatus();

        // Queue status
        $queueStatus = $this->getQueueStatus();

        // Activity feed
        $recentActivity = AuditLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $recentTickets = Ticket::with(['patient', 'department', 'service'])
            ->whereDate('visit_date', $today)
            ->latest()
            ->limit(10)
            ->get();

        // Active doctors count
        $activeDoctors = User::whereHas('role', function($q) {
            $q->where('name', 'Doctor');
        })->where('is_active', true)->count();

        return view('dashboard', compact(
            'stats', 
            'recentTickets', 
            'patientsTrend', 
            'revenueTrend', 
            'departmentDistribution',
            'ticketStatus',
            'queueStatus',
            'recentActivity',
            'activeDoctors'
        ));
    }

    public function apiData()
    {
        $today = now()->toDateString();

        $stats = [
            'total_patients' => Patient::count(),
            'today_patients' => Patient::whereDate('created_at', $today)->count(),
            'today_tickets' => Ticket::whereDate('visit_date', $today)->count(),
            'today_revenue' => Payment::whereDate('created_at', $today)->sum('amount'),
            'pending_tickets' => Ticket::whereDate('visit_date', $today)
                ->whereNull('completed_at')
                ->count(),
        ];

        $queueStatus = $this->getQueueStatus();

        $recentActivity = AuditLog::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->full_name : 'System',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'stats' => $stats,
            'queue_status' => $queueStatus,
            'activity' => $recentActivity,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getPatientsTrend($days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Patient::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => now()->parse($date)->format('M d'),
                'count' => $count,
            ];
        }
        return $data;
    }

    private function getRevenueTrend($days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sum = Payment::whereDate('created_at', $date)->sum('amount');
            $data[] = [
                'date' => now()->parse($date)->format('M d'),
                'revenue' => round($sum, 2),
            ];
        }
        return $data;
    }

    private function getDepartmentDistribution()
    {
        return Department::withCount(['tickets' => function($q) {
            $q->whereDate('visit_date', today());
        }])
        ->get()
        ->map(function($dept) {
            return [
                'name' => app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name,
                'count' => $dept->tickets_count,
            ];
        })
        ->filter(fn($d) => $d['count'] > 0);
    }

    private function getTicketStatus()
    {
        $today = today();
        return [
            'completed' => Ticket::whereDate('visit_date', $today)
                ->whereNotNull('completed_at')
                ->count(),
            'pending' => Ticket::whereDate('visit_date', $today)
                ->whereNull('completed_at')
                ->count(),
        ];
    }

    private function getQueueStatus()
    {
        $departments = Department::where('is_active', true)->get();
        $queueData = [];

        foreach ($departments as $dept) {
            $todayTickets = Ticket::where('department_id', $dept->id)
                ->whereDate('visit_date', today())
                ->orderBy('queue_number')
                ->get();

            $currentServing = $todayTickets->where('completed_at', null)
                ->where('called_number', '>', 0)
                ->first();
            
            $nextQueue = $todayTickets->where('completed_at', null)
                ->where('called_number', 0)
                ->first();

            $queueData[] = [
                'department_id' => $dept->id,
                'name' => app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name,
                'current_serving' => $currentServing ? $currentServing->queue_number : null,
                'next_queue' => $nextQueue ? $nextQueue->queue_number : null,
                'waiting' => $todayTickets->where('completed_at', null)->count(),
            ];
        }

        return $queueData;
    }
}
