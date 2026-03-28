@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات الشهري' : 'Monthly Revenue Report')
@section('page-title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات الشهري' : 'Monthly Revenue Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-stack text-success"></i> {{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات الشهري' : 'Monthly Revenue Report' }}</span>
        <div class="d-flex gap-2">
            <button onclick="exportToPDF()" class="btn btn-sm btn-danger">
                <i class="bi bi-file-pdf"></i> PDF
            </button>
            <button onclick="exportToExcel()" class="btn btn-sm btn-success">
                <i class="bi bi-file-excel"></i> Excel
            </button>
            <button onclick="exportToCSV()" class="btn btn-sm btn-info">
                <i class="bi bi-file-csv"></i> CSV
            </button>
            <button onclick="printReport()" class="btn btn-sm btn-primary">
                <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Year Filter -->
        <form action="{{ route('reports.monthly-revenue') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اختر السنة' : 'Select Year' }}</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
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
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ \App\Models\Setting::formatCurrency($totalRevenue) }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي إيرادات ' . $year : 'Total Revenue ' . $year }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ \App\Models\Setting::formatCurrency($previousYearTotal) }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي إيرادات ' . ($year - 1) : 'Total Revenue ' . ($year - 1) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card {{ $growthPercentage >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format(abs($growthPercentage), 1) }}%</h3>
                        <p>
                            {{ $growthPercentage >= 0 ? '↗' : '↘' }}
                            {{ app()->getLocale() === 'ar' ? 'نسمة النمو' : 'Growth Rate' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> {{ app()->getLocale() === 'ar' ? 'الرسم البياني للإيرادات' : 'Revenue Chart' }}
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>

        <!-- Monthly Data Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="reportTable">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الشهر' : 'Month' }}</th>
                        <th>{{ $year }}</th>
                        <th>{{ $year - 1 }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نسمة النمو' : 'Growth' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chartData as $data)
                    <tr>
                        <td>
                            {{ app()->getLocale() === 'ar' ? $data['month_ar'] : $data['month'] }}
                        </td>
                        <td><strong>{{ \App\Models\Setting::formatCurrency($data['current']) }}</strong></td>
                        <td>{{ \App\Models\Setting::formatCurrency($data['previous']) }}</td>
                        <td>
                            @if($data['growth'] >= 0)
                                <span class="text-success">↗ {{ number_format($data['growth'], 1) }}%</span>
                            @else
                                <span class="text-danger">↘ {{ number_format(abs($data['growth']), 1) }}%</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Revenue by Payment Method -->
        @if($revenueByMethod->count() > 0)
        <div class="mt-4">
            <h5>{{ app()->getLocale() === 'ar' ? 'الإيرادات حسب طريقة الدفع' : 'Revenue by Payment Method' }}</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Payment Method' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'عدد المعاملات' : 'Transactions' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByMethod as $method)
                        <tr>
                            <td>
                                @if($method->payment_method === 'cash')
                                    {{ app()->getLocale() === 'ar' ? 'نقدي' : 'Cash' }}
                                @elseif($method->payment_method === 'card')
                                    {{ app()->getLocale() === 'ar' ? 'بطاقة' : 'Card' }}
                                @elseif($method->payment_method === 'insurance')
                                    {{ app()->getLocale() === 'ar' ? 'تأمين' : 'Insurance' }}
                                @else
                                    {{ $method->payment_method }}
                                @endif
                            </td>
                            <td>{{ $method->count }}</td>
                            <td>{{ \App\Models\Setting::formatCurrency($method->total) }}</td>
                            <td>{{ number_format(($method->total / $totalRevenue) * 100, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($chartData, 'month')) !!},
        datasets: [
            {
                label: '{{ $year }}',
                data: {!! json_encode(array_column($chartData, 'current')) !!},
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            },
            {
                label: '{{ $year - 1 }}',
                data: {!! json_encode(array_column($chartData, 'previous')) !!},
                backgroundColor: 'rgba(108, 117, 125, 0.7)',
                borderColor: 'rgba(108, 117, 125, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function printReport() {
    window.print();
}

function exportToPDF() {
    window.location.href = '{{ route("reports.export.pdf", ["type" => "monthly-revenue", "year" => $year]) }}';
}

function exportToExcel() {
    window.location.href = '{{ route("reports.export.excel", ["type" => "monthly-revenue", "year" => $year]) }}';
}

function exportToCSV() {
    window.location.href = '{{ route("reports.export.csv", ["type" => "monthly-revenue", "year" => $year]) }}';
}
</script>
@endpush

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
    .btn, .form-control, .nav-link, .sidebar, .dropdown {
        display: none !important;
    }
    @page {
        size: A4 landscape;
        margin: 10mm;
    }
</style>
@endpush
