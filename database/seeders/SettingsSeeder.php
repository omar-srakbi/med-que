<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Ticket Settings
            ['key' => 'ticket_header', 'value' => 'Medical Center', 'type' => 'text', 'group' => 'ticket'],
            ['key' => 'ticket_footer', 'value' => 'Thank you for your visit', 'type' => 'text', 'group' => 'ticket'],
            ['key' => 'ticket_show_qr', 'value' => '1', 'type' => 'boolean', 'group' => 'ticket'],
            
            // Printer Settings
            ['key' => 'printer_name', 'value' => '', 'type' => 'text', 'group' => 'printer'],
            ['key' => 'printer_ip', 'value' => '', 'type' => 'text', 'group' => 'printer'],
            ['key' => 'main_door_display', 'value' => '1', 'type' => 'boolean', 'group' => 'printer'],
            
            // General Settings
            ['key' => 'clinic_name', 'value' => 'Medical Center', 'type' => 'text', 'group' => 'general'],
            ['key' => 'clinic_name_ar', 'value' => 'المركز الطبي', 'type' => 'text', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
