<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
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

        $recentTickets = Ticket::with(['patient', 'department', 'service'])
            ->whereDate('visit_date', $today)
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'recentTickets'));
    }
}
