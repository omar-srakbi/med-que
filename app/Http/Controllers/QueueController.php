<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function display()
    {
        $departments = Department::where('is_active', true)->get();
        $queueData = [];
        
        foreach ($departments as $dept) {
            $todayTickets = Ticket::where('department_id', $dept->id)
                ->whereDate('visit_date', today())
                ->orderBy('queue_number')
                ->get();
            
            $currentServing = $todayTickets->where('completed_at', null)->where('called_number', '>', 0)->first();
            $nextQueue = $todayTickets->where('completed_at', null)->where('called_number', 0)->first();
            $completedCount = $todayTickets->where('completed_at', '!=', null)->count();
            
            $queueData[$dept->id] = [
                'department' => $dept,
                'current_serving' => $currentServing,
                'next_queue' => $nextQueue,
                'completed_count' => $completedCount,
                'total_today' => $todayTickets->count(),
            ];
        }
        
        return view('queue.display', compact('queueData', 'departments'));
    }
}
