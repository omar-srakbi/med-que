<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $currentLocale = Session::get('locale', 'ar');
        $newLocale = $currentLocale === 'ar' ? 'en' : 'ar';

        Session::put('locale', $newLocale);
        App::setLocale($newLocale);

        // Get the previous URL, but avoid redirecting to language switch itself
        $previousUrl = $request->headers->get('referer');
        
        // If no referer or it's the language switch, redirect based on auth status
        if (!$previousUrl || str_contains($previousUrl, '/language/switch')) {
            if ($request->user()) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('login');
        }

        return redirect($previousUrl);
    }
}
