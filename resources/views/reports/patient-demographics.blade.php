@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'التركيبة السكانية للمرضى' : 'Patient Demographics')
@section('page-title', app()->getLocale() === 'ar' ? 'التركيبة السكانية للمرضى' : 'Patient Demographics')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'التركيبة السكانية للمرضى' : 'Patient Demographics' }}</span>
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
        <form action="{{ route('reports.patient-demographics') }}" method="GET" class="mb-4">
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

        <!-- Summary Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalPatients }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى الجدد' : 'Total New Patients' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Age Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-pie-chart"></i> {{ app()->getLocale() === 'ar' ? 'توزيع الأعمار' : 'Age Distribution' }}
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ app()->getLocale() === 'ar' ? 'الفئة العمرية' : 'Age Group' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'العدد' : 'Count' }}</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ageGroups as $group)
                                <tr>
                                    <td>
                                        @if($group->age_group === '0-17')
                                            {{ app()->getLocale() === 'ar' ? '0-17 سنة' : '0-17 years' }}
                                        @elseif($group->age_group === '18-30')
                                            {{ app()->getLocale() === 'ar' ? '18-30 سنة' : '18-30 years' }}
                                        @elseif($group->age_group === '31-45')
                                            {{ app()->getLocale() === 'ar' ? '31-45 سنة' : '31-45 years' }}
                                        @elseif($group->age_group === '46-60')
                                            {{ app()->getLocale() === 'ar' ? '46-60 سنة' : '46-60 years' }}
                                        @elseif($group->age_group === '60+')
                                            {{ app()->getLocale() === 'ar' ? '60+ سنة' : '60+ years' }}
                                        @else
                                            {{ app()->getLocale() === 'ar' ? 'غير معروف' : 'Unknown' }}
                                        @endif
                                    </td>
                                    <td>{{ $group->count }}</td>
                                    <td>{{ number_format(($group->count / $totalPatients) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Geographic Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-geo-alt"></i> {{ app()->getLocale() === 'ar' ? 'التوزيع الجغرافي' : 'Geographic Distribution' }}
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ app()->getLocale() === 'ar' ? 'مكان الميلاد' : 'Birth Place' }}</th>
                                    <th>{{ app()->getLocale() === 'ar' ? 'العدد' : 'Count' }}</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($geoDistribution as $geo)
                                <tr>
                                    <td>{{ $geo->birth_place }}</td>
                                    <td>{{ $geo->count }}</td>
                                    <td>{{ number_format(($geo->count / $totalPatients) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    .btn, .form-control { display: none !important; }
    @page { size: A4; margin: 10mm; }
}
</style>
@endpush
@endsection
