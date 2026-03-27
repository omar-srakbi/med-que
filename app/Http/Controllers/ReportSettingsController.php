<?php

namespace App\Http\Controllers;

use App\Models\ReportSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportSettingsController extends Controller
{
    public function index()
    {
        $settings = [];
        foreach (ReportSetting::all() as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }

        return view('settings.reports.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // General Settings
        ReportSetting::set('report_date_format', $request->report_date_format ?? 'Y-m-d', 'string', 'general');

        // Handle decimal separator (comma vs dot)
        $decimalSeparator = $request->report_decimal_separator ?? '.';
        if ($decimalSeparator === 'comma') {
            $decimalSeparator = ',';
        }
        ReportSetting::set('report_decimal_separator', $decimalSeparator, 'string', 'general');

        ReportSetting::set('report_decimal_places', (int) ($request->report_decimal_places ?? 2), 'integer', 'general');

        // Header
        ReportSetting::set('report_show_logo', $request->has('report_show_logo'), 'boolean', 'header');
        ReportSetting::set('report_show_clinic_name', $request->has('report_show_clinic_name'), 'boolean', 'header');
        ReportSetting::set('report_show_address', $request->has('report_show_address'), 'boolean', 'header');
        ReportSetting::set('report_show_phone', $request->has('report_show_phone'), 'boolean', 'header');
        ReportSetting::set('report_show_email', $request->has('report_show_email'), 'boolean', 'header');
        ReportSetting::set('report_custom_header', $request->report_custom_header ?? '', 'string', 'header');

        // Content
        ReportSetting::set('report_type', $request->report_type ?? 'daily', 'string', 'content');
        ReportSetting::set('report_show_totals', $request->has('report_show_totals'), 'boolean', 'content');
        ReportSetting::set('report_show_subtotals', $request->has('report_show_subtotals'), 'boolean', 'content');
        ReportSetting::set('report_show_percentages', $request->has('report_show_percentages'), 'boolean', 'content');
        ReportSetting::set('report_show_charts', $request->has('report_show_charts'), 'boolean', 'content');
        ReportSetting::set('report_show_details', $request->has('report_show_details'), 'boolean', 'content');
        ReportSetting::set('report_show_summary', $request->has('report_show_summary'), 'boolean', 'content');

        // Table Style
        ReportSetting::set('report_font_size', $request->report_font_size ?? '11', 'string', 'style');
        ReportSetting::set('report_row_spacing', $request->report_row_spacing ?? 'normal', 'string', 'style');
        ReportSetting::set('report_striped_rows', $request->has('report_striped_rows'), 'boolean', 'style');
        ReportSetting::set('report_bordered', $request->has('report_bordered'), 'boolean', 'style');
        ReportSetting::set('report_show_header_background', $request->has('report_show_header_background'), 'boolean', 'style');

        // Footer
        ReportSetting::set('report_show_generated_at', $request->has('report_show_generated_at'), 'boolean', 'footer');
        ReportSetting::set('report_show_generated_by', $request->has('report_show_generated_by'), 'boolean', 'footer');
        ReportSetting::set('report_show_page_numbers', $request->has('report_show_page_numbers'), 'boolean', 'footer');
        ReportSetting::set('report_footer_note', $request->report_footer_note ?? '', 'string', 'footer');

        // Handle logo upload
        if ($request->hasFile('report_logo')) {
            $path = $request->file('report_logo')->store('logos', 'public');
            ReportSetting::set('report_logo_path', $path, 'string', 'header');
        }

        return back()->with('success', __('Report settings saved successfully!'));
    }

    public function previewAjax(Request $request)
    {
        try {
            $settings = $request->all();

            // Get sample data based on report type
            $reportType = $settings['report_type'] ?? 'daily';
            $sampleData = $this->getSampleData($reportType);

            // Render preview HTML
            $html = view('settings.reports.preview-partial', compact('settings', 'sampleData'))->render();

            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            \Log::error('Preview error: ' . $e->getMessage());
            return response()->json([
                'html' => '<div class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2">Error: ' . e($e->getMessage()) . '</p>
                </div>'
            ], 200);
        }
    }

    private function getSampleData($type)
    {
        // Get real data for preview
        switch ($type) {
            case 'revenue':
                return [
                    'title' => app()->getLocale() === 'ar' ? 'تقرير الإيرادات' : 'Revenue Report',
                    'rows' => [
                        ['label' => app()->getLocale() === 'ar' ? 'نقدي' : 'Cash', 'value' => 1250.00, 'count' => 45],
                        ['label' => app()->getLocale() === 'ar' ? 'بطاقة' : 'Card', 'value' => 3420.50, 'count' => 28],
                        ['label' => app()->getLocale() === 'ar' ? 'تأمين' : 'Insurance', 'value' => 890.25, 'count' => 12],
                    ],
                    'total' => 5560.75,
                    'totalCount' => 85,
                ];

            case 'patients':
                return [
                    'title' => app()->getLocale() === 'ar' ? 'تقرير المرضى' : 'Patients Report',
                    'rows' => [
                        ['name' => 'أحمد محمد', 'national_id' => '1990123456', 'phone' => '0791234567', 'visits' => 3],
                        ['name' => 'فاطمة علي', 'national_id' => '1985123456', 'phone' => '0781234567', 'visits' => 5],
                        ['name' => 'محمد إبراهيم', 'national_id' => '1995123456', 'phone' => '0771234567', 'visits' => 2],
                        ['name' => 'سارة خالد', 'national_id' => '2000123456', 'phone' => '0799876543', 'visits' => 1],
                    ],
                    'total' => 11,
                ];

            case 'services':
                return [
                    'title' => app()->getLocale() === 'ar' ? 'تقرير الخدمات' : 'Services Report',
                    'rows' => [
                        ['name' => 'كشفية عامة', 'price' => 25.00, 'count' => 35, 'total' => 875.00],
                        ['name' => 'أشعة سينية', 'price' => 40.00, 'count' => 12, 'total' => 480.00],
                        ['name' => 'تحاليل دم', 'price' => 35.00, 'count' => 18, 'total' => 630.00],
                        ['name' => 'علاج طبيعي', 'price' => 50.00, 'count' => 8, 'total' => 400.00],
                    ],
                    'total' => 2385.00,
                ];

            default: // daily
                return [
                    'title' => app()->getLocale() === 'ar' ? 'التقرير اليومي' : 'Daily Report',
                    'date' => today()->format('Y-m-d'),
                    'rows' => [
                        ['department' => 'العيادة العامة', 'patients' => 25, 'revenue' => 625.00],
                        ['department' => 'الأشعة', 'patients' => 12, 'revenue' => 480.00],
                        ['department' => 'المختبر', 'patients' => 18, 'revenue' => 630.00],
                        ['department' => 'العلاج الطبيعي', 'patients' => 8, 'revenue' => 400.00],
                    ],
                    'totalPatients' => 63,
                    'totalRevenue' => 2135.00,
                ];
        }
    }

    public function export(Request $request)
    {
        $settings = json_decode($request->input('settings'), true) ?? [];
        
        // Generate PDF or Excel export here
        // For now, return a simple response
        return back()->with('success', 'Export functionality - implement PDF/Excel generation here');
    }
}
