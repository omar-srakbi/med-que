@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en)
@section('page-title', app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-bar-graph"></i> {{ app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en }}</span>
        <div class="d-flex gap-2">
            @if($isCached)
            <span class="badge bg-info">
                <i class="bi bi-hdd"></i> {{ app()->getLocale() === 'ar' ? 'مخزن' : 'Cached' }} {{ $cachedAt->diffForHumans() }}
            </span>
            @endif
            <a href="{{ route('reports.builder.edit', $report) }}" class="btn btn-sm btn-warning" title="{{ app()->getLocale() === 'ar' ? 'تعديل التقرير' : 'Edit Report' }}">
                <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
            </a>
            <button onclick="resetLabels()" class="btn btn-sm btn-outline-secondary" title="{{ app()->getLocale() === 'ar' ? 'استعادة الأسماء الأصلية' : 'Restore original names' }}">
                <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'استعادة الأسماء' : 'Restore Names' }}
            </button>
            <button onclick="printReport()" class="btn btn-sm btn-primary">
                <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
            <button onclick="resetLayout()" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'إعادة تعيين' : 'Reset' }}
            </button>
            <a href="{{ route('reports.builder.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($report->report_header)
        <div class="report-header mb-4 p-3 border-bottom">
            <h5 class="mb-2 text-primary">{{ app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en }}</h5>
            <div class="text-muted">{!! nl2br(e($report->report_header)) !!}</div>
        </div>
        @endif

        @if($report->description)
        <p class="text-muted mb-4">{{ $report->description }}</p>
        @endif

        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle"></i> 
            {{ app()->getLocale() === 'ar' ? 'اسحب حدود الأعمدة لتغيير العرض، واسحب حدود الصفوف لتغيير الارتفاع' : 'Drag column borders to change width, drag row borders to change height' }}
        </div>

        @if(count($data) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="reportTable" style="width: 100%;">
                <thead>
                    <tr>
                        @foreach(array_keys($data[0]) as $index => $key)
                        <th id="th_{{ $index }}" style="position: relative; white-space: normal; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <span class="column-header-text" style="word-wrap: break-word; cursor: pointer;" title="{{ app()->getLocale() === 'ar' ? 'انقر للتحرير' : 'Click to edit' }}" data-column="{{ $key }}">{{ $report->column_labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <div class="resizer-r" data-index="{{ $index }}" style="width: 5px; cursor: col-resize; background: transparent; height: 100%; position: absolute; {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0; top: 0;"></div>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach(array_slice($data, 0, 100) as $rowIndex => $row)
                    <tr id="tr_{{ $rowIndex }}" style="height: {{ $report->row_height ?? 40 }}px; position: relative;">
                        @foreach($row as $value)
                        <td style="overflow: visible; white-space: normal; word-wrap: break-word; vertical-align: middle; text-align: center;" title="{{ is_array($value) ? json_encode($value) : $value }}">{{ is_array($value) ? json_encode($value) : $value }}</td>
                        @endforeach
                        @if($rowIndex === 0)
                        <td class="row-resizer" rowspan="100" style="width: 10px; cursor: row-resize; background: #f8f9fa; text-align: center; vertical-align: middle; position: sticky; {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 0;">
                            <i class="bi bi-arrows-move text-muted"></i>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(count($data) > 100)
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'عرض 100 صف فقط. قم بتصدير التقرير لرؤية جميع البيانات.' : 'Showing first 100 rows only. Export report to see all data.' }}
        </div>
        @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3">{{ app()->getLocale() === 'ar' ? 'لا توجد بيانات' : 'No data found' }}</h5>
        </div>
        @endif
    </div>

    @if($report->report_footer)
    <div class="card-footer text-muted small">
        {!! nl2br(e($report->report_footer)) !!}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.resizer-r {
    user-select: none;
    touch-action: none;
}

.resizer-r:hover, .resizer-r.resizing {
    background: #0d6efd;
}

.row-resizer:hover {
    background: #0d6efd;
    color: white;
}

#reportTable th, #reportTable td {
    transition: background-color 0.2s;
}

#reportTable th:hover {
    background-color: #e9ecef;
}

/* Report header styling */
.report-header {
    background: #f8f9fa;
    border-left: 4px solid #0d6efd !important;
}

/* RTL support */
[dir="rtl"] .resizer-r {
    left: 0 !important;
    right: auto !important;
}

[dir="rtl"] .row-resizer {
    left: 0 !important;
    right: auto !important;
}

/* Column header editing */
.column-header-text {
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
    min-width: 50px;
    display: inline-block;
}

.column-header-text:hover {
    background-color: rgba(13, 110, 253, 0.1);
    outline: 2px dashed #0d6efd;
}

.column-header-input {
    width: 100%;
    padding: 4px 8px;
    border: 2px solid #0d6efd;
    border-radius: 4px;
    font-size: inherit;
    font-weight: inherit;
    background: white;
    color: #212529;
    min-width: 80px;
}

.column-header-text.editing {
    display: none;
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    .card, .card * {
        visibility: visible;
    }
    .card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .btn, .resizer-r, .row-resizer {
        display: none !important;
    }
    .alert, .badge {
        display: none !important;
    }
    .card-header, .card-footer {
        background: none !important;
        border: none !important;
    }
    .bi, .bi::before, i[class^="bi-"], i[class*=" bi-"] {
        display: none !important;
    }
    .report-header {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        margin-bottom: 20px !important;
    }
    #reportTable {
        width: 100% !important;
        font-size: 10pt;
    }
    #reportTable th, #reportTable td {
        overflow: visible !important;
        white-space: normal !important;
        word-wrap: break-word !important;
    }
    @page {
        size: A4 landscape;
        margin: 10mm;
    }
}
</style>
@endpush

@push('scripts')
<script>
let columnWidths = {};
let rowHeights = {};
let columnLabels = {};

// Load saved sizes from localStorage
function loadSizes() {
    const savedColumns = localStorage.getItem('report_{{ $report->id }}_columns');
    const savedRows = localStorage.getItem('report_{{ $report->id }}_rows');
    const savedLabels = localStorage.getItem('report_{{ $report->id }}_labels');

    if (savedColumns) {
        columnWidths = JSON.parse(savedColumns);
        Object.entries(columnWidths).forEach(([index, width]) => {
            const th = document.getElementById(`th_${index}`);
            if (th) {
                th.style.width = width + 'px';
            }
        });
    }
    // No default widths - let browser auto-size based on content

    if (savedRows) {
        rowHeights = JSON.parse(savedRows);
        Object.entries(rowHeights).forEach(([index, height]) => {
            const tr = document.getElementById(`tr_${index}`);
            if (tr) {
                tr.style.height = 'auto';
                tr.style.minHeight = height + 'px';
            }
        });
    }

    if (savedLabels) {
        columnLabels = JSON.parse(savedLabels);
        // Apply saved labels to the DOM
        Object.entries(columnLabels).forEach(([column, label]) => {
            const span = document.querySelector(`.column-header-text[data-column="${column}"]`);
            if (span) {
                span.textContent = label;
            }
        });
    }
}

// Print function
function printReport() {
    window.print();
}

// Column header editing
document.querySelectorAll('.column-header-text').forEach(span => {
    span.addEventListener('click', function(e) {
        // Don't trigger if clicking on resizer
        if (e.target.classList.contains('resizer-r')) {
            return;
        }
        makeEditable(this);
    });
});

function makeEditable(span) {
    // Prevent multiple edits
    if (span.classList.contains('editing')) {
        return;
    }
    
    const columnKey = span.dataset.column;
    const currentValue = span.textContent.trim();
    const th = span.closest('th');
    const containerDiv = th.querySelector('div');
    
    if (!containerDiv) {
        console.error('Container div not found');
        return;
    }
    
    // Create input field
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'column-header-input';
    input.value = currentValue;
    input.style.width = '150px';
    
    // Add input to container and hide span
    span.classList.add('editing');
    containerDiv.insertBefore(input, span);
    input.focus();
    input.select();
    
    // Save on blur or Enter
    function save() {
        const newValue = input.value.trim();
        
        // Update span text
        span.textContent = newValue || ucfirst(columnKey.replace(/_/g, ' '));
        span.classList.remove('editing');
        input.remove();
        
        if (newValue !== currentValue) {
            // Save to localStorage
            columnLabels[columnKey] = newValue;
            localStorage.setItem('report_{{ $report->id }}_labels', JSON.stringify(columnLabels));
            
            // Show saving indicator
            showSaveStatus('{{ app()->getLocale() === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}');
            
            // Save to server via AJAX
            saveColumnLabel(columnKey, newValue);
        }
    }
    
    input.addEventListener('blur', save);
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            save();
        } else if (e.key === 'Escape') {
            span.classList.remove('editing');
            input.remove();
        }
    });
}

function saveColumnLabel(column, label) {
    fetch('{{ route('reports.builder.update-labels', $report) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ column, label })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSaveStatus('{{ app()->getLocale() === 'ar' ? 'تم الحفظ بنجاح!' : 'Saved successfully!' }}', 'success');
        } else {
            showSaveStatus('{{ app()->getLocale() === 'ar' ? 'فشل الحفظ' : 'Failed to save' }}', 'error');
        }
    })
    .catch(err => {
        console.error('Error saving label:', err);
        showSaveStatus('{{ app()->getLocale() === 'ar' ? 'حدث خطأ' : 'Error occurred' }}', 'error');
    });
}

function showSaveStatus(message, type = 'info') {
    // Remove existing status
    const existing = document.querySelector('.save-status');
    if (existing) {
        existing.remove();
    }
    
    const status = document.createElement('div');
    status.className = `save-status position-fixed bottom-0 end-0 m-3 px-3 py-2 rounded shadow-lg text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'}`;
    status.style.zIndex = '9999';
    status.textContent = message;
    document.body.appendChild(status);
    
    setTimeout(() => {
        status.remove();
    }, 2000);
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Reset column labels to original names
function resetLabels() {
    if (confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من استعادة الأسماء الأصلية؟' : 'Are you sure you want to restore original column names?' }}')) {
        // Clear localStorage
        localStorage.removeItem('report_{{ $report->id }}_labels');
        columnLabels = {};
        
        // Reset all column headers to their original names
        document.querySelectorAll('.column-header-text').forEach(span => {
            const columnKey = span.dataset.column;
            span.textContent = ucfirst(columnKey.replace(/_/g, ' '));
        });
        
        // Save to server
        fetch('{{ route('reports.builder.update-labels', $report) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reset: true })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSaveStatus('{{ app()->getLocale() === 'ar' ? 'تم استعادة الأسماء الأصلية' : 'Original names restored' }}', 'success');
            }
        })
        .catch(err => {
            console.error('Error resetting labels:', err);
        });
    }
}

// Column resize
document.querySelectorAll('.resizer-r').forEach(resizer => {
    resizer.addEventListener('mousedown', function(e) {
        e.preventDefault();
        const index = this.dataset.index;
        const th = document.getElementById(`th_${index}`);
        const startX = e.clientX;
        const startWidth = th.offsetWidth;
        const isRTL = document.dir === 'rtl';
        
        document.body.style.cursor = 'col-resize';
        th.style.userSelect = 'none';
        
        function doDrag(e) {
            const diff = e.clientX - startX;
            // In RTL, dragging left should increase width
            const newWidth = isRTL ? startWidth - diff : startWidth + diff;
            // Allow any width between 10px and 800px for maximum flexibility
            if (newWidth >= 10 && newWidth <= 800) {
                th.style.width = newWidth + 'px';
                columnWidths[index] = newWidth;
                localStorage.setItem('report_{{ $report->id }}_columns', JSON.stringify(columnWidths));
            }
        }
        
        function stopDrag() {
            document.body.style.cursor = '';
            th.style.userSelect = '';
            document.removeEventListener('mousemove', doDrag);
            document.removeEventListener('mouseup', stopDrag);
        }
        
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', stopDrag);
    });
});

// Row resize
const rowResizer = document.querySelector('.row-resizer');
if (rowResizer) {
    rowResizer.addEventListener('mousedown', function(e) {
        e.preventDefault();
        const startY = e.clientY;
        const startHeight = document.getElementById('tr_0').offsetHeight;
        
        document.body.style.cursor = 'row-resize';
        
        function doDrag(e) {
            const newHeight = startHeight + (e.clientY - startY);
            // Allow any height between 10px and 500px for text wrapping
            if (newHeight >= 10 && newHeight <= 500) {
                document.querySelectorAll('#tableBody tr').forEach((tr, index) => {
                    tr.style.height = 'auto'; // Let content determine height
                    tr.style.minHeight = newHeight + 'px'; // But respect minimum
                    rowHeights[index] = newHeight;
                });
                localStorage.setItem('report_{{ $report->id }}_rows', JSON.stringify(rowHeights));
            }
        }
        
        function stopDrag() {
            document.body.style.cursor = '';
            document.removeEventListener('mousemove', doDrag);
            document.removeEventListener('mouseup', stopDrag);
        }
        
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', stopDrag);
    });
}

// Reset layout
function resetLayout() {
    if (confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')) {
        localStorage.removeItem('report_{{ $report->id }}_columns');
        localStorage.removeItem('report_{{ $report->id }}_rows');
        location.reload();
    }
}

// Load sizes on page load
loadSizes();
</script>
@endpush
