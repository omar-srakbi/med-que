@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إعدادات الطباعة' : 'Print Settings')
@section('page-title', app()->getLocale() === 'ar' ? 'إعدادات الطباعة' : 'Print Settings')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
</div>

<form action="{{ route('settings.printing.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Main Settings -->
        <div class="col-md-6">
            <!-- Printer Configuration -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الطابعة' : 'Printer Configuration' }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نوع الطباعة' : 'Print Mode' }}</label>
                        <select class="form-select" name="print_mode" id="print_mode">
                            <option value="browser" {{ ($settings['print_mode'] ?? 'browser') == 'browser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متصفح (تلقائي)' : 'Browser (Auto)' }}</option>
                            <option value="system" {{ ($settings['print_mode'] ?? 'browser') == 'system' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مدير الطباعة (Windows)' : 'Print Manager (Windows)' }}</option>
                            <option value="custom" {{ ($settings['print_mode'] ?? 'browser') == 'custom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مقاس مخصص' : 'Custom Size' }}</option>
                        </select>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'System يفتح نافذة الطباعة، Custom للمقاسات الخاصة' : 'System opens print dialog, Custom for special sizes' }}</small>
                    </div>
                    
                    <div id="custom_size_fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عرض الورقة (مم)' : 'Paper Width (mm)' }}</label>
                            <input type="number" class="form-control" name="print_custom_width" value="{{ $settings['print_custom_width'] ?? 80 }}" min="50" max="210">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'طول الورقة (مم)' : 'Paper Height (mm)' }}</label>
                            <input type="number" class="form-control" name="print_custom_height" value="{{ $settings['print_custom_height'] ?? 200 }}" min="100" max="350">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عدد النسخ' : 'Number of Copies' }}</label>
                        <input type="number" class="form-control" name="print_copies" value="{{ $settings['print_copies'] ?? 1 }}" min="1" max="10">
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="print_auto_print" value="1" {{ ($settings['print_auto_print'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'طباعة تلقائية' : 'Auto-Print' }}</label>
                    </div>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'طباعة فورية عند إنشاء التذكرة' : 'Instant print when ticket created' }}</small>
                </div>
            </div>
            
            <!-- Receipt Header -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-card-heading"></i> {{ app()->getLocale() === 'ar' ? 'ترويسة الإيصال' : 'Receipt Header' }}
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="receipt_show_header" value="1" {{ ($settings['receipt_show_header'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض الترويسة' : 'Show Header' }}</label>
                        <small class="text-muted d-block">{{ app()->getLocale() === 'ar' ? 'إظهار اسم العيادة في الأعلى' : 'Show clinic name at top' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'شعار العيادة' : 'Clinic Logo' }}</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="receipt_show_logo" value="1" {{ ($settings['receipt_show_logo'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض الشعار' : 'Show Logo' }}</label>
                        </div>
                        @if(isset($settings['receipt_logo_path']))
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings['receipt_logo_path']) }}" alt="Logo" style="max-height: 80px;">
                        </div>
                        @endif
                        <input type="file" class="form-control" name="receipt_logo" accept="image/*">
                    </div>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="receipt_show_clinic_name" value="1" {{ ($settings['receipt_show_clinic_name'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'اسم العيادة' : 'Clinic Name' }}</label>
                    </div>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="receipt_show_phone" value="1" {{ ($settings['receipt_show_phone'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نص مخصص' : 'Custom Text' }}</label>
                        <textarea class="form-control" name="receipt_custom_header" rows="2">{{ $settings['receipt_custom_header'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content & Format -->
        <div class="col-md-6">
            <!-- Receipt Content -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-list-check"></i> {{ app()->getLocale() === 'ar' ? 'محتوى الإيصال' : 'Receipt Content' }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_patient" value="1" {{ ($settings['receipt_show_patient'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_cashier" value="1" {{ ($settings['receipt_show_cashier'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_ticket_number" value="1" {{ ($settings['receipt_show_ticket_number'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_queue_number" value="1" {{ ($settings['receipt_show_queue_number'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue #' }}</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_visit_date" value="1" {{ ($settings['receipt_show_visit_date'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الزيارة' : 'Visit Date' }}</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_service" value="1" {{ ($settings['receipt_show_service'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="receipt_show_price" value="1" {{ ($settings['receipt_show_price'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- QR Code & Barcode -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-qr-code"></i> {{ app()->getLocale() === 'ar' ? 'QR Code والباركود' : 'QR Code & Barcode' }}
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="qr_code_enabled" value="1" {{ ($settings['qr_code_enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تفعيل QR Code' : 'Enable QR Code' }}</label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'موضع QR Code' : 'QR Code Position' }}</label>
                        <select class="form-select" name="qr_code_position">
                            <option value="bottom" {{ ($settings['qr_code_position'] ?? 'bottom') == 'bottom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'أسفل' : 'Bottom' }}</option>
                            <option value="top" {{ ($settings['qr_code_position'] ?? 'top') == 'top' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'أعلى' : 'Top' }}</option>
                        </select>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="barcode_enabled" value="1" {{ ($settings['barcode_enabled'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تفعيل الباركود' : 'Enable Barcode' }}</label>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-card-text"></i> {{ app()->getLocale() === 'ar' ? 'تذييل الإيصال' : 'Receipt Footer' }}
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="receipt_show_thank_you" value="1" {{ ($settings['receipt_show_thank_you'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'رسالة الشكر' : 'Thank You Message' }}</label>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'رسالة الشكر (عربي)' : 'Thank You (Arabic)' }}</label>
                        <input type="text" class="form-control" name="receipt_thank_you_ar" value="{{ $settings['receipt_thank_you_ar'] ?? 'شكراً لزيارتكم' }}">
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'رسالة الشكر (إنجليزي)' : 'Thank You (English)' }}</label>
                        <input type="text" class="form-control" name="receipt_thank_you_en" value="{{ $settings['receipt_thank_you_en'] ?? 'Thank you for your visit' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row mt-3">
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الإعدادات' : 'Save Settings' }}
            </button>
            <a href="{{ route('settings.printing.designer') }}" class="btn btn-warning">
                <i class="bi bi-palette"></i> {{ app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer' }}
            </a>
            <a href="{{ route('settings.printing.preview') }}" class="btn btn-info" target="_blank">
                <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة' : 'Preview' }}
            </a>
            <a href="{{ route('settings.printing.logs') }}" class="btn btn-secondary">
                <i class="bi bi-journal-text"></i> {{ app()->getLocale() === 'ar' ? 'سجل الطباعة' : 'Print Logs' }}
            </a>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
// Show/hide custom size fields
document.addEventListener('DOMContentLoaded', function() {
    const printModeSelect = document.getElementById('print_mode');
    const customSizeFields = document.getElementById('custom_size_fields');
    
    if (printModeSelect) {
        printModeSelect.addEventListener('change', function() {
            customSizeFields.style.display = (this.value === 'custom') ? 'block' : 'none';
        });
        
        // Trigger on load
        printModeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
