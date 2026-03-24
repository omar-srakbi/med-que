<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Department;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [];
        foreach (Setting::all() as $setting) {
            $settings[$setting->key] = $setting->value;
        }
        
        $departments = Department::all();
        
        return view('settings.index', compact('settings', 'departments'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Ticket settings
            'ticket_header' => 'nullable|string|max:255',
            'ticket_footer' => 'nullable|string|max:255',
            'ticket_show_qr' => 'nullable|boolean',
            
            // Printer settings
            'printer_name' => 'nullable|string|max:255',
            'printer_ip' => 'nullable|string|max:255',
            'main_door_display' => 'nullable|boolean',
            
            // General settings
            'clinic_name' => 'nullable|string|max:255',
            'clinic_name_ar' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حفظ الإعدادات بنجاح' : 'Settings saved successfully');
    }
}
