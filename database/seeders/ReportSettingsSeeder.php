<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportSetting;

class ReportSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Settings
        ReportSetting::set('report_date_format', 'Y-m-d', 'string', 'general');
        ReportSetting::set('report_currency', 'SYP', 'string', 'general');
        ReportSetting::set('report_decimal_separator', '.', 'string', 'general');
        ReportSetting::set('report_decimal_places', '2', 'integer', 'general');

        // Header
        ReportSetting::set('report_show_logo', '1', 'boolean', 'header');
        ReportSetting::set('report_show_clinic_name', '1', 'boolean', 'header');
        ReportSetting::set('report_show_address', '1', 'boolean', 'header');
        ReportSetting::set('report_show_phone', '1', 'boolean', 'header');
        ReportSetting::set('report_show_email', '0', 'boolean', 'header');
        ReportSetting::set('report_custom_header', '', 'string', 'header');

        // Content
        ReportSetting::set('report_type', 'daily', 'string', 'content');
        ReportSetting::set('report_show_totals', '1', 'boolean', 'content');
        ReportSetting::set('report_show_subtotals', '1', 'boolean', 'content');
        ReportSetting::set('report_show_percentages', '0', 'boolean', 'content');
        ReportSetting::set('report_show_charts', '1', 'boolean', 'content');
        ReportSetting::set('report_show_details', '1', 'boolean', 'content');
        ReportSetting::set('report_show_summary', '1', 'boolean', 'content');

        // Table Style
        ReportSetting::set('report_font_size', '11', 'string', 'style');
        ReportSetting::set('report_row_spacing', 'normal', 'string', 'style');
        ReportSetting::set('report_striped_rows', '1', 'boolean', 'style');
        ReportSetting::set('report_bordered', '0', 'boolean', 'style');
        ReportSetting::set('report_show_header_background', '1', 'boolean', 'style');

        // Footer
        ReportSetting::set('report_show_generated_at', '1', 'boolean', 'footer');
        ReportSetting::set('report_show_generated_by', '1', 'boolean', 'footer');
        ReportSetting::set('report_show_page_numbers', '1', 'boolean', 'footer');
        ReportSetting::set('report_footer_note', '', 'string', 'footer');
    }
}
