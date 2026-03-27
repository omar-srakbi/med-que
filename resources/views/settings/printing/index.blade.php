@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إعدادات الطباعة' : 'Print Settings')
@section('page-title', app()->getLocale() === 'ar' ? 'إعدادات الطباعة' : 'Print Settings')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
</div>

<form action="{{ route('settings.printing.update') }}" method="POST" enctype="multipart/form-data" id="printSettingsForm">
    @csrf

    <div class="row g-4">
        <!-- Settings Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-sliders"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الطابعة' : 'Printer Configuration' }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="settingsAccordion">
                        
                        <!-- Printer Configuration -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#printerSettings">
                                    <i class="bi bi-printer me-2"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الطابعة' : 'Printer Settings' }}
                                </button>
                            </h2>
                            <div id="printerSettings" class="accordion-collapse collapse show" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'نوع الطباعة' : 'Print Mode' }}</label>
                                        <select class="form-select" name="print_mode" id="print_mode">
                                            <option value="browser" {{ ($settings['print_mode'] ?? 'browser') == 'browser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متصفح (تلقائي)' : 'Browser (Auto)' }}</option>
                                            <option value="system" {{ ($settings['print_mode'] ?? 'browser') == 'system' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مدير الطباعة (Windows)' : 'Print Manager (Windows)' }}</option>
                                            <option value="custom" {{ ($settings['print_mode'] ?? 'browser') == 'custom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مقاس مخصص' : 'Custom Size' }}</option>
                                        </select>
                                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'System يفتح نافذة الطباعة، Custom للمقاسات الخاصة' : 'System opens print dialog, Custom for special sizes' }}</small>
                                    </div>

                                    <div id="custom_size_fields" class="row" style="display: none;">
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'العرض (مم)' : 'Width (mm)' }}</label>
                                            <input type="number" class="form-control" name="print_custom_width" value="{{ $settings['print_custom_width'] ?? 80 }}" min="50" max="210">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الطول (مم)' : 'Height (mm)' }}</label>
                                            <input type="number" class="form-control" name="print_custom_height" value="{{ $settings['print_custom_height'] ?? 200 }}" min="100" max="350">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'عدد النسخ' : 'Copies' }}</label>
                                            <input type="number" class="form-control" name="print_copies" value="{{ $settings['print_copies'] ?? 1 }}" min="1" max="10">
                                        </div>
                                        <div class="col-6 d-flex align-items-end">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="print_auto_print" value="1" {{ ($settings['print_auto_print'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'طباعة تلقائية' : 'Auto-Print' }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paper Settings -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paperSettings">
                                    <i class="bi bi-file-earmark me-2"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الورق' : 'Paper Settings' }}
                                </button>
                            </h2>
                            <div id="paperSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'لتصميم شكل الإيصال وترتيب العناصر، استخدم' : 'To design receipt layout and arrange elements, use' }}
                                        <a href="{{ route('settings.printing.designer') }}" class="alert-link" target="_blank">
                                            <i class="bi bi-palette"></i> {{ app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer' }}
                                        </a>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'العرض الافتراضي (مم)' : 'Default Width (mm)' }}</label>
                                            <input type="number" class="form-control" name="paper_width" value="{{ $settings['paper_width'] ?? 80 }}" min="50" max="210">
                                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يستخدم في المصمم' : 'Used in designer' }}</small>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الطول الافتراضي (مم)' : 'Default Height (mm)' }}</label>
                                            <input type="number" class="form-control" name="paper_height" value="{{ $settings['paper_height'] ?? 100 }}" min="50" max="350">
                                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يستخدم في المصمم' : 'Used in designer' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSettings">
                                    <i class="bi bi-gear me-2"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات متقدمة' : 'Advanced Settings' }}
                                </button>
                            </h2>
                            <div id="advancedSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'طابعة النظام' : 'System Printer' }}</label>
                                        <select class="form-select" name="system_printer">
                                            <option value="default">{{ app()->getLocale() === 'ar' ? 'الطابعة الافتراضية' : 'Default Printer' }}</option>
                                            <option value="thermal" {{ ($settings['system_printer'] ?? 'default') == 'thermal' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة حرارية' : 'Thermal Printer' }}</option>
                                            <option value="laser" {{ ($settings['system_printer'] ?? 'default') == 'laser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة ليزر' : 'Laser Printer' }}</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="print_silent_mode" value="1" {{ ($settings['print_silent_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الطباعة الصامتة' : 'Silent Mode' }}</label>
                                        <small class="text-muted d-block">{{ app()->getLocale() === 'ar' ? 'طباعة بدون نوافذ تأكيد' : 'Print without confirmation dialogs' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الإعدادات' : 'Save Settings' }}
                </button>
                <a href="{{ route('settings.printing.designer') }}" class="btn btn-warning" target="_blank">
                    <i class="bi bi-palette"></i> <span class="d-none d-md-inline">{{ app()->getLocale() === 'ar' ? 'المصمم' : 'Designer' }}</span>
                </a>
                <a href="{{ route('settings.printing.reports.index') }}" class="btn btn-info">
                    <i class="bi bi-file-earmark-bar-graph"></i> <span class="d-none d-md-inline">{{ app()->getLocale() === 'ar' ? 'التقارير' : 'Reports' }}</span>
                </a>
                <a href="{{ route('settings.printing.logs') }}" class="btn btn-secondary">
                    <i class="bi bi-journal-text"></i> {{ app()->getLocale() === 'ar' ? 'السجل' : 'Logs' }}
                </a>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <!-- Quick Info -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb"></i> {{ app()->getLocale() === 'ar' ? 'معلومات سريعة' : 'Quick Info' }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li class="mb-2">
                            <strong>{{ app()->getLocale() === 'ar' ? 'الطباعة' : 'Print Mode' }}:</strong>
                            <ul class="mt-1">
                                <li>{{ app()->getLocale() === 'ar' ? 'Browser: طباعة المتصفح' : 'Browser: Browser print' }}</li>
                                <li>{{ app()->getLocale() === 'ar' ? 'System: نافذة الطباعة' : 'System: Print dialog' }}</li>
                                <li>{{ app()->getLocale() === 'ar' ? 'Custom: مقاسات خاصة' : 'Custom: Special sizes' }}</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>{{ app()->getLocale() === 'ar' ? 'النسخ' : 'Copies' }}:</strong> {{ app()->getLocale() === 'ar' ? 'عدد النسخ المطبوعة' : 'Number of printed copies' }}
                        </li>
                        <li>
                            <strong>{{ app()->getLocale() === 'ar' ? 'المصمم' : 'Designer' }}:</strong> {{ app()->getLocale() === 'ar' ? 'لتصميم شكل الإيصال' : 'For receipt layout design' }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Designer Promo -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="bi bi-palette"></i> {{ app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer' }}</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">{{ app()->getLocale() === 'ar' ? 'صمم شكل الإيصال بنفسك!' : 'Design your receipt layout!' }}</p>
                    <ul class="small mb-3">
                        <li>{{ app()->getLocale() === 'ar' ? 'ترتيب العناصر' : 'Arrange elements' }}</li>
                        <li>{{ app()->getLocale() === 'ar' ? 'تحديد المواقع' : 'Position elements' }}</li>
                        <li>{{ app()->getLocale() === 'ar' ? 'تغيير الأحجام' : 'Change sizes' }}</li>
                        <li>{{ app()->getLocale() === 'ar' ? 'معاينة مباشرة' : 'Live preview' }}</li>
                    </ul>
                    <a href="{{ route('settings.printing.designer') }}" class="btn btn-warning btn-sm w-100" target="_blank>
                        <i class="bi bi-box-arrow-up-right"></i> {{ app()->getLocale() === 'ar' ? 'افتح المصمم' : 'Open Designer' }}
                    </a>
                </div>
            </div>
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
