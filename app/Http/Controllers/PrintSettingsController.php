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
        // Get receipt settings
        $receiptSettings = [];
        foreach (PrintSetting::where('category', 'receipt')->get() as $setting) {
            $receiptSettings[$setting->setting_key] = $setting->setting_value;
        }

        // Get report settings
        $reportSettings = [];
        foreach (PrintSetting::where('category', 'report')->get() as $setting) {
            $reportSettings[$setting->setting_key] = $setting->setting_value;
        }

        $templates = PrintTemplate::where('template_type', 'receipt')
            ->orderBy('is_default', 'desc')
            ->get();

        $printLogs = PrintLog::with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('settings.printing.index', compact('receiptSettings', 'reportSettings', 'templates', 'printLogs'));
    }

    public function update(Request $request)
    {
        // Keep old method for backwards compatibility
        return $this->updateReceipt($request);
    }

    public function updateReceipt(Request $request)
    {
        // Receipt Print Settings
        PrintSetting::set('receipt_print_mode', $request->receipt_print_mode ?? 'browser', 'string', 'receipt');
        PrintSetting::set('receipt_printer_name', $request->receipt_printer_name ?? 'default', 'string', 'receipt');
        PrintSetting::set('receipt_copies', (int) ($request->receipt_copies ?? 1), 'integer', 'receipt');
        PrintSetting::set('receipt_auto_print', $request->has('receipt_auto_print'), 'boolean', 'receipt');

        // Custom paper size for receipts
        PrintSetting::set('receipt_custom_width', (int) ($request->receipt_custom_width ?? 80), 'integer', 'receipt');
        PrintSetting::set('receipt_custom_height', (int) ($request->receipt_custom_height ?? 200), 'integer', 'receipt');

        return back()->with('success', __('Receipt print settings saved successfully!'));
    }

    public function updateReport(Request $request)
    {
        // Report Print Settings
        PrintSetting::set('report_print_mode', $request->report_print_mode ?? 'browser', 'string', 'report');
        PrintSetting::set('report_printer_name', $request->report_printer_name ?? 'default', 'string', 'report');
        PrintSetting::set('report_paper_size', $request->report_paper_size ?? 'A4', 'string', 'report');
        PrintSetting::set('report_orientation', $request->report_orientation ?? 'portrait', 'string', 'report');
        PrintSetting::set('report_copies', (int) ($request->report_copies ?? 1), 'integer', 'report');
        PrintSetting::set('report_auto_print', $request->has('report_auto_print'), 'boolean', 'report');
        PrintSetting::set('report_show_header', $request->has('report_show_header'), 'boolean', 'report');
        PrintSetting::set('report_show_footer', $request->has('report_show_footer'), 'boolean', 'report');

        // Custom paper size for reports
        PrintSetting::set('report_custom_width', (int) ($request->report_custom_width ?? 210), 'integer', 'report');
        PrintSetting::set('report_custom_height', (int) ($request->report_custom_height ?? 297), 'integer', 'report');

        return back()->with('success', __('Report print settings saved successfully!'));
    }

    public function getSettings($type)
    {
        $settings = [];
        $category = $type === 'receipt' ? 'receipt' : 'report';
        
        foreach (PrintSetting::where('category', $category)->get() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }

        return response()->json($settings);
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

    public function previewAjax(Request $request)
    {
        // Get a sample ticket for preview
        $ticket = \App\Models\Ticket::with(['patient', 'department', 'service', 'cashier', 'payment'])
            ->latest()
            ->first();

        if (!$ticket) {
            return response()->json(['html' => '<div class="text-center text-muted py-5"><p>No tickets available for preview</p></div>']);
        }

        // Use submitted settings
        $settings = $request->all();

        // Render preview HTML
        $html = view('tickets.receipt-partial', compact('ticket', 'settings'))->render();

        return response()->json(['html' => $html]);
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
