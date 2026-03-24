<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch()
    {
        $currentLocale = Session::get('locale', 'ar');
        $newLocale = $currentLocale === 'ar' ? 'en' : 'ar';
        
        Session::put('locale', $newLocale);
        App::setLocale($newLocale);
        
        return back();
    }
}
