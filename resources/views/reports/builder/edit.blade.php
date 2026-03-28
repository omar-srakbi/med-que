@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل تقرير' : 'Edit Report')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل تقرير' : 'Edit Report')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل التقرير' : 'Edit Report' }}</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('reports.builder.update', $report) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم (عربي)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $report->name_ar) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم (إنجليزي)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $report->name_en) }}" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $report->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نوع التقرير' : 'Report Type' }}</label>
                        <select name="report_type" class="form-select" required>
                            <option value="simple" {{ $report->report_type === 'simple' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'بسيط' : 'Simple' }}</option>
                            @if($canUseAdvanced)
                            <option value="advanced" {{ $report->report_type === 'advanced' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متقدم' : 'Advanced' }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مصدر البيانات' : 'Data Source' }}</label>
                        <select name="data_source" class="form-select" required>
                            @foreach($dataSources as $key => $source)
                            <option value="{{ $key }}" {{ $report->data_source === $key ? 'selected' : '' }}>{{ $source['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عام' : 'Public' }}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_public" id="is_public" {{ $report->is_public ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">
                                {{ app()->getLocale() === 'ar' ? 'جعل التقرير متاحاً للجميع' : 'Make report available to everyone' }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الأعمدة وتسمياتها' : 'Columns and Their Labels' }} <span class="text-danger">*</span></label>
                <div class="border rounded p-3">
                    @if(is_array($report->columns))
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i> 
                        {{ app()->getLocale() === 'ar' ? 'قم بتسمية كل عمود باسم مخصص. اترك الحقل فارغاً لاستخدام الاسم الافتراضي.' : 'Enter a custom name for each column. Leave empty to use the default name.' }}
                    </div>
                    <div class="row">
                        @foreach($report->columns as $index => $col)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <strong>{{ app()->getLocale() === 'ar' ? 'العمود' : 'Column' }} #{{ $index + 1 }}</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $col }}" checked id="col_{{ $index }}">
                                        <label class="form-check-label fw-bold" for="col_{{ $index }}">{{ $col }}</label>
                                    </div>
                                    <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'الاسم المخصص' : 'Custom Name' }}:</label>
                                    <input type="text" class="form-control" 
                                           name="column_labels[{{ $col }}]" 
                                           value="{{ $report->column_labels[$col] ?? '' }}"
                                           placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب الاسم المخصص...' : 'Enter custom name...' }}">
                                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'اتركه فارغاً للافتراضي' : 'Leave empty for default' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'إعدادات الجدول' : 'Table Settings' }}</label>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'عرض العمود (px)' : 'Column Width (px)' }}</label>
                        <input type="number" name="column_width" class="form-control" value="{{ $report->column_width ?? 150 }}" min="50" max="500">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'ارتفاع الصف (px)' : 'Row Height (px)' }}</label>
                        <input type="number" name="row_height" class="form-control" value="{{ $report->row_height ?? 40 }}" min="20" max="200">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'ترويسة التقرير' : 'Report Header' }}</label>
                <textarea name="report_header" class="form-control" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب نص الترويسة هنا...' : 'Enter header text here...' }}">{{ old('report_header', $report->report_header) }}</textarea>
                <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في أعلى التقرير وعند الطباعة' : 'Appears at top of report and when printing' }}</small>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'تذييل التقرير' : 'Report Footer' }}</label>
                <textarea name="report_footer" class="form-control" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب نص التذييل هنا...' : 'Enter footer text here...' }}">{{ old('report_footer', $report->report_footer) }}</textarea>
                <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يظهر في أسفل التقرير وعند الطباعة' : 'Appears at bottom of report and when printing' }}</small>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'التخزين المؤقت' : 'Caching' }}</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="cache_enabled" id="cache_enabled" {{ $report->cache_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="cache_enabled">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل التخزين المؤقت' : 'Enable caching' }}
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مدة التخزين (دقائق)' : 'Cache Duration (minutes)' }}</label>
                <input type="number" name="cache_duration_minutes" class="form-control" value="{{ $report->cache_duration_minutes ?? 10 }}" min="1" max="1440">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
                </button>
                <span id="unsavedIndicator" class="badge bg-warning text-dark" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i> {{ app()->getLocale() === 'ar' ? 'تغييرات غير محفوظة' : 'Unsaved Changes' }}
                </span>
                <a href="{{ route('reports.builder.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let hasUnsavedChanges = false;
const unsavedIndicator = document.getElementById('unsavedIndicator');
const saveBtn = document.getElementById('saveBtn');

// Track changes
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('change', () => {
        hasUnsavedChanges = true;
        updateIndicator();
    });
    element.addEventListener('input', () => {
        hasUnsavedChanges = true;
        updateIndicator();
    });
});

// Update visual indicator
function updateIndicator() {
    if (hasUnsavedChanges) {
        unsavedIndicator.style.display = 'inline-block';
        saveBtn.innerHTML = '<i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'احفظ قبل المتابعة' : 'Save Before Leaving' }}';
    } else {
        unsavedIndicator.style.display = 'none';
        saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}';
    }
}

// Warn before leaving
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

// Form submission
document.querySelector('form').addEventListener('submit', function() {
    hasUnsavedChanges = false;
    updateIndicator();
});
</script>
@endpush
