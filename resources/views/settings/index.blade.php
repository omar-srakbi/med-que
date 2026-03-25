@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings')
@section('page-title', app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings')

@section('content')
<div class="row">
    <!-- Ticket Settings -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات التذاكر' : 'Ticket Settings' }}
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عنوان التذكرة' : 'Ticket Header' }}</label>
                        <input type="text" class="form-control" name="ticket_header" value="{{ $settings['ticket_header'] ?? '' }}">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في أعلى التذكرة' : 'Appears at top of ticket' }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'تذييل التذكرة' : 'Ticket Footer' }}</label>
                        <input type="text" class="form-control" name="ticket_footer" value="{{ $settings['ticket_footer'] ?? '' }}">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في أسفل التذكرة' : 'Appears at bottom of ticket' }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'تنسيق رقم التذكرة' : 'Ticket Number Format' }}</label>
                        <select class="form-select" name="ticket_format" id="ticket_format">
                            <option value="{prefix}-{date}-{seq}" {{ ($settings['ticket_format'] ?? '') == '{prefix}-{date}-{seq}' ? 'selected' : '' }}>TKT-20260323-0001</option>
                            <option value="{prefix}/{seq}/{date}" {{ ($settings['ticket_format'] ?? '') == '{prefix}/{seq}/{date}' ? 'selected' : '' }}>TKT/0001/20260323</option>
                            <option value="{dept}-{date}-{seq}" {{ ($settings['ticket_format'] ?? '') == '{dept}-{date}-{seq}' ? 'selected' : '' }}>CLI-20260323-0001</option>
                        </select>
                        <small class="text-muted">
                            {prefix} = {{ app()->getLocale() === 'ar' ? 'البادئة' : 'Prefix' }},
                            {date} = {{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }},
                            {seq} = {{ app()->getLocale() === 'ar' ? 'التسلسل' : 'Sequence' }},
                            {dept} = {{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}
                        </small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="ticket_show_qr" value="1" {{ ($settings['ticket_show_qr'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض QR Code على التذكرة' : 'Show QR Code on ticket' }}</label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات التذاكر' : 'Save Ticket Settings' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Currency Settings -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-stack"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات العملة' : 'Currency Settings' }}
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="currency_code" class="form-label">{{ app()->getLocale() === 'ar' ? 'رمز العملة' : 'Currency Code' }}</label>
                        <input type="text" class="form-control" id="currency_code" name="currency_code" value="{{ old('currency_code', \App\Models\Setting::getCurrencyCode()) }}" placeholder="e.g., JOD, USD, EUR">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'مثال: JOD, USD, EUR, SAR' : 'Example: JOD, USD, EUR, SAR' }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="currency_symbol" class="form-label">{{ app()->getLocale() === 'ar' ? 'رمز العملة' : 'Currency Symbol' }}</label>
                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', \App\Models\Setting::getCurrencySymbol()) }}" placeholder="e.g., JD, $, €, £">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'مثال: JD, $, €, £' : 'Example: JD, $, €, £' }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="currency_decimals" class="form-label">{{ app()->getLocale() === 'ar' ? 'عدد الخانات العشرية' : 'Decimal Places' }}</label>
                        <select class="form-select" id="currency_decimals" name="currency_decimals">
                            <option value="0" {{ old('currency_decimals', \App\Models\Setting::getCurrencyDecimals()) == 0 ? 'selected' : '' }}>0</option>
                            <option value="1" {{ old('currency_decimals', \App\Models\Setting::getCurrencyDecimals()) == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('currency_decimals', \App\Models\Setting::getCurrencyDecimals()) == 2 ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('currency_decimals', \App\Models\Setting::getCurrencyDecimals()) == 3 ? 'selected' : '' }}>3</option>
                        </select>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'عدد الأرقام بعد الفاصلة' : 'Number of digits after decimal point' }}</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات العملة' : 'Save Currency Settings' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Printer Settings -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الطباعة' : 'Printer Settings' }}
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الطابعة' : 'Printer Name' }}</label>
                        <input type="text" class="form-control" name="printer_name" value="{{ $settings['printer_name'] ?? '' }}" placeholder="EPSON TM-T20">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'اسم الطابعة الرئيسية' : 'Main printer name' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عنوان IP للطابعة' : 'Printer IP Address' }}</label>
                        <input type="text" class="form-control" name="printer_ip" value="{{ $settings['printer_ip'] ?? '' }}" placeholder="192.168.1.100">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'للطابعات الشبكية' : 'For network printers' }}</small>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="main_door_display" value="1" {{ ($settings['main_door_display'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تفعيل عرض الباب الرئيسي' : 'Enable Main Door Display' }}</label>
                        <small class="text-muted d-block">{{ app()->getLocale() === 'ar' ? 'لعرض الأرقام على الشاشة الكبيرة' : 'For displaying numbers on big screen' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'حجم الورق' : 'Paper Size' }}</label>
                        <select class="form-select" name="paper_size">
                            <option value="80mm" {{ ($settings['paper_size'] ?? '') == '80mm' ? 'selected' : '' }}>80mm (Standard)</option>
                            <option value="58mm" {{ ($settings['paper_size'] ?? '') == '58mm' ? 'selected' : '' }}>58mm (Small)</option>
                            <option value="a4" {{ ($settings['paper_size'] ?? '') == 'a4' ? 'selected' : '' }}>A4</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الإعدادات' : 'Save Settings' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- General Settings -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear"></i> {{ app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'General Settings' }}
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="clinic_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم العيادة (إنجليزي)' : 'Clinic Name (English)' }}</label>
                        <input type="text" class="form-control" id="clinic_name" name="clinic_name" value="{{ old('clinic_name', $settings['clinic_name'] ?? 'Medical Center') }}" placeholder="Medical Center">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في واجهة النظام' : 'Appears in system header' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clinic_name_ar" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم العيادة (عربي)' : 'Clinic Name (Arabic)' }}</label>
                        <input type="text" class="form-control" id="clinic_name_ar" name="clinic_name_ar" value="{{ old('clinic_name_ar', $settings['clinic_name_ar'] ?? 'المركز الطبي') }}" placeholder="المركز الطبي">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في واجهة النظام' : 'Appears in system header' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="default_language" class="form-label">{{ app()->getLocale() === 'ar' ? 'اللغة الافتراضية' : 'Default Language' }}</label>
                        <select class="form-select" id="default_language" name="default_language">
                            <option value="ar" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="en" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'لغة النظام الافتراضية' : 'Default system language' }}</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الإعدادات' : 'Save Settings' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Department Ticket Settings -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات أقسام التذاكر' : 'Department Ticket Settings' }}
            </div>
            <div class="card-body">
                <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'يمكنك تخصيص إعدادات التذاكر لكل قسم من صفحة الأقسام' : 'You can customize ticket settings per department from Departments page' }}</p>
                <a href="{{ route('departments.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'الذهاب إلى الأقسام' : 'Go to Departments' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
