<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }
        
        $logs = $query->paginate(20);
        $users = User::all();
        $actions = ['created', 'updated', 'deleted', 'logged_in', 'logged_out'];
        
        return view('audit-logs.index', compact('logs', 'users', 'actions'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();
        
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->get();
        
        $csvData = "ID,Date,User,Action,Model,Description,IP Address\n";
        
        foreach ($logs as $log) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->full_name : 'System',
                $log->action,
                $log->model_type ? class_basename($log->model_type) : '-',
                str_replace(',', ';', $log->description),
                $log->ip_address
            );
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');
    }
}
