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
        // General settings
        if ($request->has('clinic_name')) {
            \App\Models\Setting::set('clinic_name', $request->clinic_name);
        }
        if ($request->has('clinic_name_ar')) {
            \App\Models\Setting::set('clinic_name_ar', $request->clinic_name_ar);
        }
        if ($request->has('default_language')) {
            \App\Models\Setting::set('default_language', $request->default_language);
        }
        
        // Currency settings
        if ($request->has('currency_code')) {
            \App\Models\Setting::set('currency_code', $request->currency_code);
        }
        if ($request->has('currency_symbol')) {
            \App\Models\Setting::set('currency_symbol', $request->currency_symbol);
        }
        if ($request->has('currency_decimals')) {
            \App\Models\Setting::set('currency_decimals', $request->currency_decimals);
        }
        
        // Ticket settings
        if ($request->has('ticket_header')) {
            \App\Models\Setting::set('ticket_header', $request->ticket_header);
        }
        if ($request->has('ticket_footer')) {
            \App\Models\Setting::set('ticket_footer', $request->ticket_footer);
        }
        if ($request->has('ticket_format')) {
            \App\Models\Setting::set('ticket_format', $request->ticket_format);
        }
        if ($request->has('ticket_show_qr')) {
            \App\Models\Setting::set('ticket_show_qr', true);
        } else {
            \App\Models\Setting::set('ticket_show_qr', false);
        }

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حفظ الإعدادات بنجاح' : 'Settings saved successfully');
    }
}
