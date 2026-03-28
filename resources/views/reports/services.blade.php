@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'أداء الخدمات' : 'Services Report')
@section('page-title', app()->getLocale() === 'ar' ? 'أداء الخدمات' : 'Services Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-heart-pulse"></i> {{ app()->getLocale() === 'ar' ? 'أداء الخدمات' : 'Services Report' }}</span>
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
        <form action="{{ route('reports.services') }}" method="GET" class="mb-4">
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
                        <h3>{{ $totalServices }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي الخدمات' : 'Total Services' }}</p>
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

        <!-- Services Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="reportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'العدد' : 'Count' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النسبة' : 'Percentage' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicesPerformance as $index => $service)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name }}</td>
                        <td>{{ \App\Models\Setting::formatCurrency($service->price) }}</td>
                        <td><strong>{{ $service->count }}</strong></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" style="width: {{ ($service->count / $totalServices) * 100 }}%">
                                    {{ number_format(($service->count / $totalServices) * 100, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td>{{ \App\Models\Setting::formatCurrency($service->total_revenue) }}</td>
                        <td>{{ number_format(($service->total_revenue / $totalRevenue) * 100, 1) }}%</td>
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
    .btn, .form-control { display: none !important; }
    @page { size: A4 landscape; margin: 10mm; }
}
</style>
@endpush
@endsection
