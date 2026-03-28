@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report')
@section('page-title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-stack"></i> {{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report' }}</span>
        <div class="d-flex gap-2">
            <button onclick="printReport()" class="btn btn-sm btn-success">
                <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form action="{{ route('reports.daily-revenue') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اختر التاريخ' : 'Select Date' }}</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ \App\Models\Setting::formatCurrency($totalRevenue) }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalTransactions }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'عدد المعاملات' : 'Total Transactions' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue by Department -->
        <h5 class="mb-3">{{ app()->getLocale() === 'ar' ? 'الإيرادات حسب القسم' : 'Revenue by Department' }}</h5>
        @if($revenueByDept->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueByDept as $dept)
                    <tr>
                        <td>{{ app()->getLocale() === 'ar' ? $dept->department->name_ar : $dept->department->name }}</td>
                        <td><strong>{{ \App\Models\Setting::formatCurrency($dept->total) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد إيرادات' : 'No revenue' }}</p>
        @endif
        
        <!-- Payments List -->
        <h5 class="mb-3 mt-4">{{ app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments' }}</h5>
        @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'رقم الإيصال' : 'Receipt #' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التذكرة' : 'Ticket' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->ticket->ticket_number }}</td>
                        <td>{{ $payment->ticket->patient->full_name }}</td>
                        <td>{{ $payment->cashier->full_name }}</td>
                        <td><strong>{{ \App\Models\Setting::formatCurrency($payment->amount) }}</strong></td>
                        <td>{{ $payment->created_at->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد مدفوعات' : 'No payments' }}</p>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
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
    /* Hide non-essential elements */
    .btn, .form-control, .nav-link, .sidebar, .dropdown {
        display: none !important;
    }
    /* Page orientation */
    @page {
        size: A4;
        margin: 10mm;
    }
    /* Force landscape if set */
    body.report-landscape {
        @page {
            size: A4 landscape;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let reportSettings = null;

// Load report settings on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('api.print-settings.get', 'report') }}")
        .then(response => response.json())
        .then(settings => {
            reportSettings = settings;
            console.log('Report settings loaded:', settings);
        })
        .catch(err => console.error('Error loading report settings:', err));
});

function printReport() {
    if (!reportSettings) {
        alert('{{ app()->getLocale() === 'ar' ? 'جاري تحميل إعدادات الطباعة...' : 'Loading print settings...' }}');
        return;
    }

    // Apply orientation
    const orientation = reportSettings.report_orientation || 'portrait';
    if (orientation === 'landscape') {
        document.body.classList.add('report-landscape');
    }

    // Apply paper size
    const paperSize = reportSettings.report_paper_size || 'A4';
    const width = reportSettings.report_custom_width || (paperSize === 'Letter' ? '216mm' : (paperSize === 'Legal' ? '216mm' : '210mm'));
    const height = reportSettings.report_custom_height || (paperSize === 'Letter' ? '279mm' : (paperSize === 'Legal' ? '356mm' : '297mm'));

    // Print using browser or system
    const printMode = reportSettings.report_print_mode || 'browser';
    
    if (printMode === 'system') {
        // Open print dialog
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Report</title>
                <style>
                    @page { size: ${paperSize} ${orientation}; margin: 10mm; }
                    body { font-family: Arial, sans-serif; margin: 0; padding: 10mm; }
                    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
                </style>
            </head>
            <body>
                ${document.querySelector('.card').innerHTML}
                <script>
                    setTimeout(function() { window.print(); }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    } else {
        // Browser print
        window.print();
    }

    // Remove landscape class after print
    setTimeout(() => {
        document.body.classList.remove('report-landscape');
    }, 1000);
}
</script>
@endpush
