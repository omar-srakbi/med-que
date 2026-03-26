<?php

namespace Database\Seeders;

use App\Models\PrintSetting;
use App\Models\PrintTemplate;
use Illuminate\Database\Seeder;

class PrintSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // General Print Settings
        $settings = [
            // Printer Settings
            ['key' => 'print_default_printer', 'value' => 'system_default', 'type' => 'string', 'category' => 'general'],
            ['key' => 'print_printer_type', 'value' => 'thermal_80mm', 'type' => 'string', 'category' => 'general'],
            ['key' => 'print_paper_size', 'value' => '80mm', 'type' => 'string', 'category' => 'general'],
            ['key' => 'print_auto_print', 'value' => false, 'type' => 'boolean', 'category' => 'general'],
            ['key' => 'print_copies', 'value' => 1, 'type' => 'integer', 'category' => 'general'],
            
            // Receipt Header
            ['key' => 'receipt_show_logo', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_clinic_name', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_address', 'value' => false, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_phone', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_email', 'value' => false, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_custom_header', 'value' => '', 'type' => 'string', 'category' => 'receipt'],
            
            // Receipt Content
            ['key' => 'receipt_show_patient', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_cashier', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_ticket_number', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_queue_number', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_visit_date', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_service', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_price', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_show_payment_method', 'value' => false, 'type' => 'boolean', 'category' => 'receipt'],
            
            // Receipt Footer
            ['key' => 'receipt_show_thank_you', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'receipt_thank_you_ar', 'value' => 'شكراً لزيارتكم', 'type' => 'string', 'category' => 'receipt'],
            ['key' => 'receipt_thank_you_en', 'value' => 'Thank you for your visit', 'type' => 'string', 'category' => 'receipt'],
            ['key' => 'receipt_custom_footer', 'value' => '', 'type' => 'string', 'category' => 'receipt'],
            
            // QR Code Settings
            ['key' => 'qr_code_enabled', 'value' => true, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'qr_code_content', 'value' => 'ticket_url', 'type' => 'string', 'category' => 'receipt'],
            ['key' => 'qr_code_position', 'value' => 'bottom', 'type' => 'string', 'category' => 'receipt'],
            ['key' => 'qr_code_size', 'value' => 100, 'type' => 'integer', 'category' => 'receipt'],
            
            // Barcode Settings
            ['key' => 'barcode_enabled', 'value' => false, 'type' => 'boolean', 'category' => 'receipt'],
            ['key' => 'barcode_format', 'value' => 'Code128', 'type' => 'string', 'category' => 'receipt'],
            ['key' => 'barcode_content', 'value' => 'ticket_number', 'type' => 'string', 'category' => 'receipt'],
            
            // Font Settings
            ['key' => 'print_font_family', 'value' => 'Arial', 'type' => 'string', 'category' => 'format'],
            ['key' => 'print_font_size', 'value' => 'medium', 'type' => 'string', 'category' => 'format'],
            ['key' => 'print_text_align', 'value' => 'center', 'type' => 'string', 'category' => 'format'],
            
            // Language Settings
            ['key' => 'print_language', 'value' => 'auto', 'type' => 'string', 'category' => 'format'], // auto, arabic, english, bilingual
            ['key' => 'print_bilingual', 'value' => false, 'type' => 'boolean', 'category' => 'format'],
        ];

        foreach ($settings as $setting) {
            PrintSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $setting['category']
            );
        }

        // Create Default Templates
        $templates = [
            [
                'name' => 'Minimal Receipt',
                'description' => 'Basic receipt with essential information only',
                'template_type' => 'receipt',
                'is_default' => false,
                'template_data' => [
                    'show_logo' => false,
                    'show_header' => true,
                    'show_patient' => true,
                    'show_service' => true,
                    'show_price' => true,
                    'show_footer' => false,
                    'show_qr_code' => false,
                    'compact_mode' => true,
                ],
            ],
            [
                'name' => 'Standard Receipt',
                'description' => 'Default receipt with all standard information',
                'template_type' => 'receipt',
                'is_default' => true,
                'template_data' => [
                    'show_logo' => true,
                    'show_header' => true,
                    'show_patient' => true,
                    'show_cashier' => true,
                    'show_service' => true,
                    'show_price' => true,
                    'show_footer' => true,
                    'show_qr_code' => true,
                    'compact_mode' => false,
                ],
            ],
            [
                'name' => 'Detailed Receipt',
                'description' => 'Complete receipt with all available information',
                'template_type' => 'receipt',
                'is_default' => false,
                'template_data' => [
                    'show_logo' => true,
                    'show_header' => true,
                    'show_clinic_info' => true,
                    'show_patient' => true,
                    'show_cashier' => true,
                    'show_ticket_number' => true,
                    'show_queue_number' => true,
                    'show_visit_date' => true,
                    'show_service' => true,
                    'show_price' => true,
                    'show_payment_method' => true,
                    'show_footer' => true,
                    'show_qr_code' => true,
                    'show_barcode' => true,
                    'compact_mode' => false,
                ],
            ],
        ];

        foreach ($templates as $template) {
            PrintTemplate::create([
                'name' => $template['name'],
                'description' => $template['description'],
                'template_type' => $template['template_type'],
                'is_default' => $template['is_default'],
                'template_data' => $template['template_data'],
                'created_by' => null, // Set to null to avoid foreign key issues
            ]);
        }
    }
}
