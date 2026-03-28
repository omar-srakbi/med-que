@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'أداء الأقسام' : 'Department Performance')
@section('page-title', app()->getLocale() === 'ar' ? 'أداء الأقسام' : 'Department Performance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up"></i> {{ app()->getLocale() === 'ar' ? 'أداء الأقسام' : 'Department Performance' }}</span>
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
        <form action="{{ route('reports.department-performance') }}" method="GET" class="mb-4">
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
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalVisits }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي الزيارات' : 'Total Visits' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ \App\Models\Setting::formatCurrency($totalRevenue) }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="reportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الزيارات' : 'Visits' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النسبة' : 'Percentage' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue' }}</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departmentPerformance as $index => $dept)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name }}</td>
                        <td><strong>{{ $dept->visits }}</strong></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" style="width: {{ ($dept->visits / $totalVisits) * 100 }}%">
                                    {{ number_format(($dept->visits / $totalVisits) * 100, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Setting::formatCurrency($dept->revenue) }}</td>
                        <td>{{ number_format(($dept->revenue / $totalRevenue) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endpush
@endsection
