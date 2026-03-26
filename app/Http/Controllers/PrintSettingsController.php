<?php

namespace App\Http\Controllers;

use App\Models\PrintSetting;
use App\Models\PrintTemplate;
use App\Models\PrintLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrintSettingsController extends Controller
{
    public function index()
    {
        $settings = [];
        foreach (PrintSetting::all() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }
        
        $templates = PrintTemplate::where('template_type', 'receipt')
            ->orderBy('is_default', 'desc')
            ->get();
        
        $printLogs = PrintLog::with('user')
            ->latest()
            ->limit(20)
            ->get();
        
        return view('settings.printing.index', compact('settings', 'templates', 'printLogs'));
    }

    public function update(Request $request)
    {
        // General Settings
        PrintSetting::set('print_mode', $request->print_mode ?? 'browser', 'string', 'general');
        PrintSetting::set('print_copies', (int) ($request->print_copies ?? 1), 'integer', 'general');
        PrintSetting::set('print_auto_print', $request->has('print_auto_print'), 'boolean', 'general');
        
        // Custom paper size
        PrintSetting::set('print_custom_width', (int) ($request->print_custom_width ?? 80), 'integer', 'general');
        PrintSetting::set('print_custom_height', (int) ($request->print_custom_height ?? 200), 'integer', 'general');
        
        // Receipt Header
        PrintSetting::set('receipt_show_header', $request->has('receipt_show_header'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_logo', $request->has('receipt_show_logo'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_clinic_name', $request->has('receipt_show_clinic_name'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_phone', $request->has('receipt_show_phone'), 'boolean', 'receipt');
        PrintSetting::set('receipt_custom_header', $request->receipt_custom_header ?? '', 'string', 'receipt');
        
        // Receipt Content
        PrintSetting::set('receipt_show_patient', $request->has('receipt_show_patient'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_cashier', $request->has('receipt_show_cashier'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_ticket_number', $request->has('receipt_show_ticket_number'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_queue_number', $request->has('receipt_show_queue_number'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_visit_date', $request->has('receipt_show_visit_date'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_service', $request->has('receipt_show_service'), 'boolean', 'receipt');
        PrintSetting::set('receipt_show_price', $request->has('receipt_show_price'), 'boolean', 'receipt');
        
        // Receipt Footer
        PrintSetting::set('receipt_show_thank_you', $request->has('receipt_show_thank_you'), 'boolean', 'receipt');
        PrintSetting::set('receipt_thank_you_ar', $request->receipt_thank_you_ar ?? '', 'string', 'receipt');
        PrintSetting::set('receipt_thank_you_en', $request->receipt_thank_you_en ?? '', 'string', 'receipt');
        
        // QR Code
        PrintSetting::set('qr_code_enabled', $request->has('qr_code_enabled'), 'boolean', 'receipt');
        PrintSetting::set('qr_code_position', $request->qr_code_position ?? 'bottom', 'string', 'receipt');
        
        // Barcode
        PrintSetting::set('barcode_enabled', $request->has('barcode_enabled'), 'boolean', 'receipt');
        
        // Handle logo upload
        if ($request->hasFile('receipt_logo')) {
            $path = $request->file('receipt_logo')->store('logos', 'public');
            PrintSetting::set('receipt_logo_path', $path, 'string', 'receipt');
        }
        
        return back()->with('success', __('Print settings saved successfully!'));
    }

    public function setDefaultTemplate($id)
    {
        $template = PrintTemplate::findOrFail($id);
        $template->setAsDefault();
        
        return back()->with('success', __('Template set as default!'));
    }

    public function deleteTemplate($id)
    {
        $template = PrintTemplate::findOrFail($id);
        
        if ($template->is_default) {
            return back()->withErrors(['error' => __('Cannot delete default template')]);
        }
        
        $template->delete();
        
        return back()->with('success', __('Template deleted successfully!'));
    }

    public function previewReceipt()
    {
        // Get a sample ticket for preview
        $ticket = \App\Models\Ticket::with(['patient', 'department', 'service', 'cashier', 'payment'])
            ->latest()
            ->first();
        
        if (!$ticket) {
            return back()->withErrors(['error' => __('No tickets available for preview')]);
        }
        
        // Get print settings
        $settings = [];
        foreach (\App\Models\PrintSetting::all() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }
        
        return view('tickets.receipt-preview', compact('ticket', 'settings'));
    }

    public function designer()
    {
        // Get a sample ticket for preview
        $ticket = \App\Models\Ticket::with(['patient', 'department', 'service', 'cashier'])
            ->latest()
            ->first();
        
        if (!$ticket) {
            return back()->withErrors(['error' => __('No tickets available for preview')]);
        }
        
        // Get print settings
        $settings = [];
        foreach (\App\Models\PrintSetting::all() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }
        
        return view('settings.printing.designer', compact('ticket', 'settings'));
    }
    
    public function saveLayout(Request $request)
    {
        $validated = $request->validate([
            'layout' => 'required|array',
            'paper_width' => 'nullable|integer|min:50|max:210',
            'paper_height' => 'nullable|integer|min:50|max:350',
        ]);

        PrintSetting::set('receipt_layout', json_encode($validated['layout']), 'json', 'receipt');
        PrintSetting::set('paper_width', $validated['paper_width'] ?? 80, 'integer', 'general');
        PrintSetting::set('paper_height', $validated['paper_height'] ?? 100, 'integer', 'general');

        return response()->json(['success' => true]);
    }

    public function printLogs()
    {
        $logs = PrintLog::with('user')
            ->latest('printed_at')
            ->paginate(50);
        
        return view('settings.printing.logs', compact('logs'));
    }

    public function exportLogs()
    {
        $logs = PrintLog::with('user')
            ->latest('printed_at')
            ->get();
        
        $csvData = "ID,User,Type,Record,Printer,Copies,Status,Date\n";
        
        foreach ($logs as $log) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->user ? $log->user->full_name : 'System',
                $log->print_type,
                $log->record_type . '#' . $log->record_id,
                $log->printer_name ?? 'Default',
                $log->copies,
                $log->status,
                $log->printed_at->format('Y-m-d H:i')
            );
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="print_logs_' . date('Y-m-d') . '.csv"');
    }
}
