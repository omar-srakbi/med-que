@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إنشاء تقرير' : 'Create Report')
@section('page-title', app()->getLocale() === 'ar' ? 'إنشاء تقرير' : 'Create Report')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-file-earmark-plus"></i> {{ app()->getLocale() === 'ar' ? 'إنشاء تقرير مخصص' : 'Create Custom Report' }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.builder.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم (عربي)' : 'Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم (إنجليزي)' : 'Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'نوع التقرير' : 'Report Type' }} <span class="text-danger">*</span></label>
                        <select name="report_type" class="form-select" required>
                            <option value="simple">{{ app()->getLocale() === 'ar' ? 'بسيط' : 'Simple' }}</option>
                            @if($canUseAdvanced)
                            <option value="advanced">{{ app()->getLocale() === 'ar' ? 'متقدم' : 'Advanced' }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'مصدر البيانات' : 'Data Source' }} <span class="text-danger">*</span></label>
                        <select name="data_source" class="form-select" required>
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر...' : 'Select...' }}</option>
                            @foreach($dataSources as $key => $source)
                            <option value="{{ $key }}">{{ $source['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'عام' : 'Public' }}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_public" id="is_public">
                            <label class="form-check-label" for="is_public">
                                {{ app()->getLocale() === 'ar' ? 'جعل التقرير متاحاً للجميع' : 'Make report available to everyone' }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الأعمدة' : 'Columns' }} <span class="text-danger">*</span></label>
                <div id="columns_container" class="border rounded p-3">
                    <p class="text-muted small">{{ app()->getLocale() === 'ar' ? 'اختر مصدر البيانات أولاً' : 'Select data source first' }}</p>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'إعدادات الجدول' : 'Table Settings' }}</label>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'عرض العمود (px)' : 'Column Width (px)' }}</label>
                        <input type="number" name="column_width" class="form-control" value="150" min="50" max="500">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'ارتفاع الصف (px)' : 'Row Height (px)' }}</label>
                        <input type="number" name="row_height" class="form-control" value="40" min="20" max="200">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'التخزين المؤقت' : 'Caching' }}</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="cache_enabled" id="cache_enabled" checked>
                    <label class="form-check-label" for="cache_enabled">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل التخزين المؤقت للبيانات الكبيرة' : 'Enable caching for large datasets' }}
                    </label>
                </div>
                <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'سيتم تخزين التقارير التي تحتوي على أكثر من 50,000 صف تلقائياً' : 'Reports with more than 50,000 rows will be cached automatically' }}</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التقرير' : 'Save Report' }}
                </button>
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
const dataSources = @json($dataSources);

document.querySelector('[name="data_source"]').addEventListener('change', function() {
    const source = dataSources[this.value];
    const container = document.getElementById('columns_container');
    
    if (!source) {
        container.innerHTML = '<p class="text-muted small">{{ app()->getLocale() === 'ar' ? 'اختر مصدر البيانات أولاً' : 'Select data source first' }}</p>';
        return;
    }
    
    let html = '<div class="row">';
    if (source.columns && Array.isArray(source.columns)) {
        source.columns.forEach(col => {
            html += `
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="columns[]" value="${col}" id="col_${col}">
                        <label class="form-check-label" for="col_${col}">${col}</label>
                    </div>
                </div>
            `;
        });
    }
    html += '</div>';
    
    container.innerHTML = html;
});
</script>
@endpush
