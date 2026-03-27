@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إعدادات التقارير' : 'Reports Settings')
@section('page-title', app()->getLocale() === 'ar' ? 'إعدادات التقارير' : 'Reports Settings')

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

<form action="{{ route('settings.printing.reports.update') }}" method="POST" enctype="multipart/form-data" id="reportsSettingsForm">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Settings -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-sliders"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات التقارير' : 'Reports Settings' }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="settingsAccordion">
                        
                        <!-- General Settings -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalSettings">
                                    <i class="bi bi-gear me-2"></i> {{ app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'General Settings' }}
                                </button>
                            </h2>
                            <div id="generalSettings" class="accordion-collapse collapse show" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'تنسيق التاريخ' : 'Date Format' }}</label>
                                        <select class="form-select" name="report_date_format" id="report_date_format">
                                            <option value="Y-m-d" {{ ($settings['report_date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>2024-03-27 (ISO)</option>
                                            <option value="d/m/Y" {{ ($settings['report_date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>27/03/2024 (Arabic)</option>
                                            <option value="m/d/Y" {{ ($settings['report_date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>03/27/2024 (US)</option>
                                            <option value="d F Y" {{ ($settings['report_date_format'] ?? 'Y-m-d') == 'd F Y' ? 'selected' : '' }}>27 March 2024 (Long)</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الفاصل العشري' : 'Decimal Separator' }}</label>
                                            <select class="form-select" name="report_decimal_separator">
                                                <option value="." {{ (isset($settings['report_decimal_separator']) ? $settings['report_decimal_separator'] : '.') === '.' ? 'selected' : '' }}>. (نقطة)</option>
                                                <option value="comma" {{ (isset($settings['report_decimal_separator']) ? $settings['report_decimal_separator'] : '.') === 'comma' ? 'selected' : '' }}>, (فاصلة)</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المنازل العشرية' : 'Decimal Places' }}</label>
                                            <input type="number" class="form-control" name="report_decimal_places" value="{{ $settings['report_decimal_places'] ?? 2 }}" min="0" max="4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Header -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#headerSettings">
                                    <i class="bi bi-card-heading me-2"></i> {{ app()->getLocale() === 'ar' ? 'ترويسة التقرير' : 'Report Header' }}
                                </button>
                            </h2>
                            <div id="headerSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3 p-3 bg-light rounded">
                                        <label class="form-label fw-bold mb-2">{{ app()->getLocale() === 'ar' ? 'شعار العيادة' : 'Clinic Logo' }}</label>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="report_show_logo" value="1" {{ ($settings['report_show_logo'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'عرض الشعار' : 'Show Logo' }}</label>
                                        </div>
                                        @if(isset($settings['report_logo_path']))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings['report_logo_path']) }}" alt="Logo" style="max-height: 60px;" class="rounded border bg-white p-2">
                                        </div>
                                        @endif
                                        <input type="file" class="form-control form-control-sm" name="report_logo" accept="image/*">
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_clinic_name" value="1" {{ ($settings['report_show_clinic_name'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'اسم العيادة' : 'Clinic Name' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_address" value="1" {{ ($settings['report_show_address'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_phone" value="1" {{ ($settings['report_show_phone'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_email" value="1" {{ ($settings['report_show_email'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نص مخصص' : 'Custom Text' }}</label>
                                        <textarea class="form-control form-control-sm" name="report_custom_header" rows="2">{{ $settings['report_custom_header'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Content -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentSettings">
                                    <i class="bi bi-list-check me-2"></i> {{ app()->getLocale() === 'ar' ? 'محتوى التقرير' : 'Report Content' }}
                                </button>
                            </h2>
                            <div id="contentSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ app()->getLocale() === 'ar' ? 'نوع التقرير' : 'Report Type' }}</label>
                                        <select class="form-select" name="report_type" id="report_type">
                                            <option value="daily" {{ ($settings['report_type'] ?? 'daily') == 'daily' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تقرير يومي' : 'Daily Report' }}</option>
                                            <option value="revenue" {{ ($settings['report_type'] ?? 'daily') == 'revenue' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات' : 'Revenue Report' }}</option>
                                            <option value="patients" {{ ($settings['report_type'] ?? 'daily') == 'patients' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تقرير المرضى' : 'Patients Report' }}</option>
                                            <option value="services" {{ ($settings['report_type'] ?? 'daily') == 'services' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تقرير الخدمات' : 'Services Report' }}</option>
                                        </select>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_totals" value="1" {{ ($settings['report_show_totals'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الإجماليات' : 'Totals' }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_subtotals" value="1" {{ ($settings['report_show_subtotals'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'المجاميع الجزئية' : 'Subtotals' }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_percentages" value="1" {{ ($settings['report_show_percentages'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'النسب المئوية' : 'Percentages' }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_charts" value="1" {{ ($settings['report_show_charts'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الرسوم البيانية' : 'Charts' }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_details" value="1" {{ ($settings['report_show_details'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check p-2 border rounded">
                                                <input class="form-check-input" type="checkbox" name="report_show_summary" value="1" {{ ($settings['report_show_summary'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'الملخص' : 'Summary' }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Style -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tableSettings">
                                    <i class="bi bi-table me-2"></i> {{ app()->getLocale() === 'ar' ? 'نمط الجدول' : 'Table Style' }}
                                </button>
                            </h2>
                            <div id="tableSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'حجم الخط' : 'Font Size' }}</label>
                                        <select class="form-select" name="report_font_size">
                                            <option value="10" {{ ($settings['report_font_size'] ?? '10') == '10' ? 'selected' : '' }}>10pt - صغير</option>
                                            <option value="11" {{ ($settings['report_font_size'] ?? '10') == '11' ? 'selected' : '' }}>11pt - متوسط</option>
                                            <option value="12" {{ ($settings['report_font_size'] ?? '10') == '12' ? 'selected' : '' }}>12pt - كبير</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'تباعد الصفوف' : 'Row Spacing' }}</label>
                                        <select class="form-select" name="report_row_spacing">
                                            <option value="compact" {{ ($settings['report_row_spacing'] ?? 'compact') == 'compact' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مضغوط' : 'Compact' }}</option>
                                            <option value="normal" {{ ($settings['report_row_spacing'] ?? 'compact') == 'normal' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'عادي' : 'Normal' }}</option>
                                            <option value="comfortable" {{ ($settings['report_row_spacing'] ?? 'compact') == 'comfortable' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مريح' : 'Comfortable' }}</option>
                                        </select>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_striped_rows" value="1" {{ ($settings['report_striped_rows'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'صفوف مخططة' : 'Striped Rows' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_bordered" value="1" {{ ($settings['report_bordered'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'حدود الجدول' : 'Table Borders' }}</label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="report_show_header_background" value="1" {{ ($settings['report_show_header_background'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'خلفية الترويسة' : 'Header Background' }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#footerSettings">
                                    <i class="bi bi-card-text me-2"></i> {{ app()->getLocale() === 'ar' ? 'تذييل التقرير' : 'Report Footer' }}
                                </button>
                            </h2>
                            <div id="footerSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                                <div class="accordion-body">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_generated_at" value="1" {{ ($settings['report_show_generated_at'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Generated Date' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_generated_by" value="1" {{ ($settings['report_show_generated_by'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'تم الإنشاء بواسطة' : 'Generated By' }}</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="report_show_page_numbers" value="1" {{ ($settings['report_show_page_numbers'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'أرقام الصفحات' : 'Page Numbers' }}</label>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'ملاحظة ختامية' : 'Footer Note' }}</label>
                                        <textarea class="form-control form-control-sm" name="report_footer_note" rows="2">{{ $settings['report_footer_note'] ?? '' }}</textarea>
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
                <a href="{{ route('settings.printing.designer') }}" class="btn btn-warning" title="{{ app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer' }}">
                    <i class="bi bi-palette"></i>
                </a>
                <button type="button" class="btn btn-success" onclick="exportPreview()">
                    <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'تصدير' : 'Export' }}
                </button>
            </div>
        </div>

        <!-- Right Column: Live Preview -->
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة مباشرة' : 'Live Preview' }}</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" onclick="updatePreview()">
                            <i class="bi bi-arrow-clockwise"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Refresh' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="togglePreviewSize()">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    </div>
                    <div id="reportPreview" class="bg-white border shadow-sm p-3" style="min-height: 500px; max-height: 70vh; overflow-y: auto; font-size: 11px;">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ app()->getLocale() === 'ar' ? 'جاري تحميل المعاينة...' : 'Loading preview...' }}</p>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'معاينة تقريبية - قد يختلف الشكل النهائي' : 'Approximate preview - final output may vary' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('styles')
<style>
    .accordion-button {
        font-weight: 500;
        padding: 1rem;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: var(--bs-primary);
    }
    .accordion-body {
        padding: 1rem;
    }
    .form-check.p-2.border.rounded {
        transition: all 0.2s;
    }
    .form-check.p-2.border.rounded:hover {
        background-color: rgba(13, 110, 253, 0.05);
        border-color: var(--bs-primary);
    }
    .sticky-top {
        transition: all 0.3s;
    }
    #reportPreview {
        transition: all 0.3s;
    }
    #reportPreview.fullscreen {
        max-height: 90vh !important;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        width: 90%;
        max-width: 800px;
    }
    .preview-table {
        width: 100%;
        font-size: 10px;
        border-collapse: collapse;
    }
    .preview-table th, .preview-table td {
        padding: 4px 6px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    .preview-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .preview-table.striped tbody tr:nth-child(even) {
        background-color: rgba(0,0,0,0.02);
    }
    .preview-table.bordered th, .preview-table.bordered td {
        border: 1px solid #dee2e6;
    }
    .preview-table.bordered {
        border: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports settings page loaded');
    
    // Add change listeners to all inputs for live preview
    const form = document.getElementById('reportsSettingsForm');
    form.addEventListener('change', debounce(updatePreview, 500));
    
    // Also listen for changes on report type selector
    const reportTypeSelect = document.getElementById('report_type');
    if (reportTypeSelect) {
        reportTypeSelect.addEventListener('change', function() {
            console.log('Report type changed to:', this.value);
            updatePreview();
        });
    }

    // Initial preview load
    updatePreview();
});

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Update preview
function updatePreview() {
    const previewDiv = document.getElementById('reportPreview');
    console.log('Updating preview...');
    
    previewDiv.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-hourglass-split" style="font-size: 2rem;"></i><p class="mt-2">' + (document.documentElement.lang === 'ar' ? 'جاري التحديث...' : 'Updating...') + '</p></div>';

    const formData = new FormData(document.getElementById('reportsSettingsForm'));
    const settings = {};
    formData.forEach((value, key) => {
        settings[key] = value;
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        settings[cb.name] = cb.checked ? '1' : '0';
    });

    console.log('Sending settings:', settings);

    fetch('{{ route("settings.printing.reports.preview.ajax") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(settings)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server error response:', text);
                throw new Error('Server error: ' + response.status);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Preview data received:', data);
        if (data.html) {
            previewDiv.innerHTML = data.html;
        } else {
            previewDiv.innerHTML = '<div class="text-center text-danger"><i class="bi bi-exclamation-circle"></i><p>No HTML returned</p></div>';
        }
    })
    .catch(err => {
        console.error('Preview error:', err);
        previewDiv.innerHTML = '<div class="text-center text-danger"><i class="bi bi-exclamation-circle"></i><p>Error: ' + err.message + '</p></div>';
    });
}

// Toggle fullscreen preview
function togglePreviewSize() {
    const preview = document.getElementById('reportPreview');
    preview.classList.toggle('fullscreen');
}

// Export preview
function exportPreview() {
    const formData = new FormData(document.getElementById('reportsSettingsForm'));
    const settings = {};
    formData.forEach((value, key) => {
        settings[key] = value;
    });
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        settings[cb.name] = cb.checked ? '1' : '0';
    });

    // Create a form and submit for export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("settings.printing.reports.export") }}';
    
    const settingsInput = document.createElement('input');
    settingsInput.type = 'hidden';
    settingsInput.name = 'settings';
    settingsInput.value = JSON.stringify(settings);
    form.appendChild(settingsInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush
