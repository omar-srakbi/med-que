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

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3" id="printSettingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="receipt-tab" data-bs-toggle="tab" data-bs-target="#receipt" type="button" role="tab">
            <i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الإيصالات' : 'Receipt Settings' }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#report" type="button" role="tab">
            <i class="bi bi-file-earmark-bar-graph"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات التقارير' : 'Report Settings' }}
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="printSettingsTabsContent">

    <!-- Receipt Settings Tab -->
    <div class="tab-pane fade show active" id="receipt" role="tabpanel">
        <form action="{{ route('settings.printing.updateReceipt') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات طباعة الإيصالات' : 'Receipt Print Settings' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'الطابعة' : 'Printer' }}</label>
                                <select class="form-select" name="receipt_printer_name">
                                    <option value="default">{{ app()->getLocale() === 'ar' ? 'الطابعة الافتراضية' : 'Default Printer' }}</option>
                                    <option value="thermal" {{ ($receiptSettings['receipt_printer_name'] ?? '') == 'thermal' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة حرارية' : 'Thermal Printer' }}</option>
                                    <option value="pos" {{ ($receiptSettings['receipt_printer_name'] ?? '') == 'pos' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة نقاط بيع' : 'POS Printer' }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'نوع الطباعة' : 'Print Mode' }}</label>
                                <select class="form-select" name="receipt_print_mode" id="receipt_print_mode">
                                    <option value="browser" {{ ($receiptSettings['receipt_print_mode'] ?? 'browser') == 'browser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متصفح (تلقائي)' : 'Browser (Auto)' }}</option>
                                    <option value="system" {{ ($receiptSettings['receipt_print_mode'] ?? 'browser') == 'system' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مدير الطباعة (Windows)' : 'Print Manager (Windows)' }}</option>
                                    <option value="custom" {{ ($receiptSettings['receipt_print_mode'] ?? 'browser') == 'custom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مقاس مخصص' : 'Custom Size' }}</option>
                                </select>
                            </div>

                            <div id="receipt_custom_size_fields" class="row" style="display: none;">
                                <div class="col-6">
                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'العرض (مم)' : 'Width (mm)' }}</label>
                                    <input type="number" class="form-control" name="receipt_custom_width" value="{{ $receiptSettings['receipt_custom_width'] ?? 80 }}" min="50" max="210">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الطول (مم)' : 'Height (mm)' }}</label>
                                    <input type="number" class="form-control" name="receipt_custom_height" value="{{ $receiptSettings['receipt_custom_height'] ?? 200 }}" min="100" max="350">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'عدد النسخ' : 'Copies' }}</label>
                                    <input type="number" class="form-control" name="receipt_copies" value="{{ $receiptSettings['receipt_copies'] ?? 1 }}" min="1" max="10">
                                </div>
                                <div class="col-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="receipt_auto_print" value="1" {{ ($receiptSettings['receipt_auto_print'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'طباعة تلقائية' : 'Auto-Print' }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات الإيصالات' : 'Save Receipt Settings' }}
                    </button>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-lightbulb"></i> {{ app()->getLocale() === 'ar' ? 'معلومات سريعة' : 'Quick Info' }}</h6>
                        </div>
                        <div class="card-body">
                            <h6>{{ app()->getLocale() === 'ar' ? 'الإعدادات الحالية' : 'Current Settings' }}</h6>
                            <ul class="small mb-0">
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'الطابعة' : 'Printer' }}:</strong> {{ ($receiptSettings['receipt_printer_name'] ?? 'default') == 'default' ? (app()->getLocale() === 'ar' ? 'الافتراضية' : 'Default') : (($receiptSettings['receipt_printer_name'] ?? '') == 'thermal' ? (app()->getLocale() === 'ar' ? 'حرارية' : 'Thermal') : 'POS') }}</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'الحجم' : 'Size' }}:</strong> {{ $receiptSettings['receipt_custom_width'] ?? 80 }}mm × {{ $receiptSettings['receipt_custom_height'] ?? 200 }}mm</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'النسخ' : 'Copies' }}:</strong> {{ $receiptSettings['receipt_copies'] ?? 1 }}</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto' }}:</strong> {{ ($receiptSettings['receipt_auto_print'] ?? false) ? (app()->getLocale() === 'ar' ? 'نعم' : 'Yes') : (app()->getLocale() === 'ar' ? 'لا' : 'No') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Settings Tab -->
    <div class="tab-pane fade" id="report" role="tabpanel">
        <form action="{{ route('settings.printing.updateReport') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-bar-graph"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات طباعة التقارير' : 'Report Print Settings' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'الطابعة' : 'Printer' }}</label>
                                <select class="form-select" name="report_printer_name">
                                    <option value="default">{{ app()->getLocale() === 'ar' ? 'الطابعة الافتراضية' : 'Default Printer' }}</option>
                                    <option value="laser" {{ ($reportSettings['report_printer_name'] ?? '') == 'laser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة ليزر' : 'Laser Printer' }}</option>
                                    <option value="inkjet" {{ ($reportSettings['report_printer_name'] ?? '') == 'inkjet' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة حبر' : 'Inkjet Printer' }}</option>
                                    <option value="network" {{ ($reportSettings['report_printer_name'] ?? '') == 'network' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طابعة شبكة' : 'Network Printer' }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'نوع الطباعة' : 'Print Mode' }}</label>
                                <select class="form-select" name="report_print_mode" id="report_print_mode">
                                    <option value="browser" {{ ($reportSettings['report_print_mode'] ?? 'browser') == 'browser' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متصفح (تلقائي)' : 'Browser (Auto)' }}</option>
                                    <option value="system" {{ ($reportSettings['report_print_mode'] ?? 'browser') == 'system' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مدير الطباعة (Windows)' : 'Print Manager (Windows)' }}</option>
                                    <option value="custom" {{ ($reportSettings['report_print_mode'] ?? 'browser') == 'custom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مقاس مخصص' : 'Custom Size' }}</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'حجم الورق' : 'Paper Size' }}</label>
                                    <select class="form-select" name="report_paper_size" id="report_paper_size">
                                        <option value="A4" {{ ($reportSettings['report_paper_size'] ?? 'A4') == 'A4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                                        <option value="Letter" {{ ($reportSettings['report_paper_size'] ?? 'A4') == 'Letter' ? 'selected' : '' }}>Letter (216 × 279 mm)</option>
                                        <option value="Legal" {{ ($reportSettings['report_paper_size'] ?? 'A4') == 'Legal' ? 'selected' : '' }}>Legal (216 × 356 mm)</option>
                                        <option value="Custom" {{ ($reportSettings['report_paper_size'] ?? 'A4') == 'Custom' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مخصص' : 'Custom' }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'الاتجاه' : 'Orientation' }}</label>
                                    <select class="form-select" name="report_orientation">
                                        <option value="portrait" {{ ($reportSettings['report_orientation'] ?? 'portrait') == 'portrait' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'طولي' : 'Portrait' }}</option>
                                        <option value="landscape" {{ ($reportSettings['report_orientation'] ?? 'portrait') == 'landscape' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'عرضي' : 'Landscape' }}</option>
                                    </select>
                                </div>
                            </div>

                            <div id="report_custom_size_fields" class="row mt-3" style="display: none;">
                                <div class="col-6">
                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'العرض (مم)' : 'Width (mm)' }}</label>
                                    <input type="number" class="form-control" name="report_custom_width" value="{{ $reportSettings['report_custom_width'] ?? 210 }}" min="50" max="350">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الطول (مم)' : 'Height (mm)' }}</label>
                                    <input type="number" class="form-control" name="report_custom_height" value="{{ $reportSettings['report_custom_height'] ?? 297 }}" min="50" max="500">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'عدد النسخ' : 'Copies' }}</label>
                                    <input type="number" class="form-control" name="report_copies" value="{{ $reportSettings['report_copies'] ?? 1 }}" min="1" max="10">
                                </div>
                                <div class="col-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="report_auto_print" value="1" {{ ($reportSettings['report_auto_print'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'طباعة تلقائية' : 'Auto-Print' }}</label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">{{ app()->getLocale() === 'ar' ? 'خيارات إضافية' : 'Additional Options' }}</h6>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="report_show_header" value="1" {{ ($reportSettings['report_show_header'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض الترويسة' : 'Show Header' }}</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="report_show_footer" value="1" {{ ($reportSettings['report_show_footer'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض التذييل' : 'Show Footer' }}</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-3">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات التقارير' : 'Save Report Settings' }}
                    </button>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-lightbulb"></i> {{ app()->getLocale() === 'ar' ? 'معلومات سريعة' : 'Quick Info' }}</h6>
                        </div>
                        <div class="card-body">
                            <h6>{{ app()->getLocale() === 'ar' ? 'الإعدادات الحالية' : 'Current Settings' }}</h6>
                            <ul class="small mb-0">
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'الطابعة' : 'Printer' }}:</strong> {{ ($reportSettings['report_printer_name'] ?? 'default') == 'default' ? (app()->getLocale() === 'ar' ? 'الافتراضية' : 'Default') : (($reportSettings['report_printer_name'] ?? '') == 'laser' ? (app()->getLocale() === 'ar' ? 'ليزر' : 'Laser') : (($reportSettings['report_printer_name'] ?? '') == 'inkjet' ? (app()->getLocale() === 'ar' ? 'حبر' : 'Inkjet') : (app()->getLocale() === 'ar' ? 'شبكة' : 'Network'))) }}</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'الحجم' : 'Size' }}:</strong> {{ $reportSettings['report_paper_size'] ?? 'A4' }} ({{ ($reportSettings['report_orientation'] ?? 'portrait') == 'portrait' ? (app()->getLocale() === 'ar' ? 'طولي' : 'Portrait') : (app()->getLocale() === 'ar' ? 'عرضي' : 'Landscape') }})</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'النسخ' : 'Copies' }}:</strong> {{ $reportSettings['report_copies'] ?? 1 }}</li>
                                <li><strong>{{ app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto' }}:</strong> {{ ($reportSettings['report_auto_print'] ?? false) ? (app()->getLocale() === 'ar' ? 'نعم' : 'Yes') : (app()->getLocale() === 'ar' ? 'لا' : 'No') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'ملاحظة' : 'Note' }}</h6>
                        </div>
                        <div class="card-body small">
                            <p class="mb-0">{{ app()->getLocale() === 'ar' ? 'يمكنك تغيير هذه الإعدادات في أي وقت دون التأثير على إعدادات الإيصالات.' : 'You can change these settings anytime without affecting receipt settings.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Receipt custom size fields
document.addEventListener('DOMContentLoaded', function() {
    const receiptPrintMode = document.getElementById('receipt_print_mode');
    const receiptCustomSize = document.getElementById('receipt_custom_size_fields');

    if (receiptPrintMode) {
        receiptPrintMode.addEventListener('change', function() {
            receiptCustomSize.style.display = (this.value === 'custom') ? 'block' : 'none';
        });
        receiptPrintMode.dispatchEvent(new Event('change'));
    }

    // Report custom size fields
    const reportPrintMode = document.getElementById('report_print_mode');
    const reportCustomSize = document.getElementById('report_custom_size_fields');
    const reportPaperSize = document.getElementById('report_paper_size');

    if (reportPaperSize) {
        reportPaperSize.addEventListener('change', function() {
            reportCustomSize.style.display = (this.value === 'Custom') ? 'block' : 'none';
        });
        reportPaperSize.dispatchEvent(new Event('change'));
    }

    if (reportPrintMode) {
        reportPrintMode.addEventListener('change', function() {
            reportCustomSize.style.display = (this.value === 'custom' || reportPaperSize.value === 'Custom') ? 'block' : 'none';
        });
    }
});
</script>
@endpush
