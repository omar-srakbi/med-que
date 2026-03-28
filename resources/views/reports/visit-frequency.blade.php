@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تكرار الزيارات' : 'Visit Frequency')
@section('page-title', app()->getLocale() === 'ar' ? 'تكرار الزيارات' : 'Visit Frequency')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-repeat"></i> {{ app()->getLocale() === 'ar' ? 'تكرار الزيارات' : 'Visit Frequency' }}</span>
        <div class="d-flex gap-2">
            <button onclick="printReport()" class="btn btn-sm btn-primary">
                <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form action="{{ route('reports.visit-frequency') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'من تاريخ' : 'From Date' }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'إلى تاريخ' : 'To Date' }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalPatients }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى' : 'Total Patients' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $returningPatients }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'المرضى العائدين' : 'Returning Patients' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card {{ $returningRate >= 50 ? 'bg-info' : 'bg-warning' }} text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($returningRate, 1) }}%</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'نسبة العودة' : 'Return Rate' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Frequent Visitors Table -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-trophy"></i> {{ app()->getLocale() === 'ar' ? 'أكثر المرضى زيارة' : 'Most Frequent Visitors' }}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="reportTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'عدد الزيارات' : 'Visits' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'إجمالي ما دفع' : 'Total Spent' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($frequentVisitors as $index => $visitor)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $visitor->patient->first_name ?? 'N/A' }} {{ $visitor->patient->last_name ?? '' }}</strong>
                                </td>
                                <td>{{ $visitor->patient->national_id ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $visitor->visit_count }}</span>
                                </td>
                                <td>{{ \App\Models\Setting::formatCurrency($visitor->total_spent) }}</td>
                                <td>{{ $visitor->patient->phone ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printReport() {
    window.print();
}
</script>
@endpush

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, .form-control, .nav-link, .sidebar, .dropdown { display: none !important; }
    
    /* Ensure summary cards row prints properly */
    .row.mb-4 {
        display: block !important;
        margin-bottom: 1rem !important;
    }
    .row.mb-4::after {
        content: "" !important;
        display: table !important;
        clear: both !important;
    }
    .row.mb-4 [class*="col-"] {
        float: left !important;
        width: 33.33% !important;
        padding: 0.5rem !important;
        box-sizing: border-box !important;
    }
    .row.mb-4 .card {
        margin-bottom: 0.5rem !important;
        border: 2px solid #333 !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
    }
    .row.mb-4 .card.bg-primary { background: #e7f1ff !important; color: #000 !important; }
    .row.mb-4 .card.bg-success { background: #d1e7dd !important; color: #000 !important; }
    .row.mb-4 .card.bg-info { background: #cff4fc !important; color: #000 !important; }
    .row.mb-4 .card.bg-warning { background: #fff3cd !important; color: #000 !important; }
    
    /* Ensure card content is visible */
    .row.mb-4 .card h3 {
        font-size: 24pt !important;
        font-weight: bold !important;
        color: #000 !important;
        margin: 0.5rem 0 !important;
        display: block !important;
    }
    .row.mb-4 .card p {
        font-size: 12pt !important;
        color: #333 !important;
        display: block !important;
        margin: 0.25rem 0 !important;
    }
    .row.mb-4 .card .card-body {
        padding: 1rem !important;
        text-align: center !important;
        display: block !important;
    }
    .row.mb-4 .card .card-body * {
        visibility: visible !important;
        display: block !important;
    }
    
    @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endpush
@endsection
