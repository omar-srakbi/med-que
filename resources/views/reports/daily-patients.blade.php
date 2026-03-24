@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تقرير المرضى اليومي' : 'Daily Patients Report')
@section('page-title', app()->getLocale() === 'ar' ? 'تقرير المرضى اليومي' : 'Daily Patients Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'تقرير المرضى اليومي' : 'Daily Patients Report' }}</span>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form action="{{ route('reports.daily-patients') }}" method="GET" class="mb-4">
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
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalPatients }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'مريض جديد' : 'New Patients' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Department Visits -->
        <h5 class="mb-3">{{ app()->getLocale() === 'ar' ? 'الزيارات حسب القسم' : 'Visits by Department' }}</h5>
        @if($departmentVisits->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'عدد الزيارات' : 'Visit Count' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departmentVisits as $visit)
                    <tr>
                        <td>{{ app()->getLocale() === 'ar' ? $visit->department->name_ar : $visit->department->name }}</td>
                        <td><span class="badge bg-primary">{{ $visit->count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات' : 'No visits' }}</p>
        @endif
        
        <!-- New Patients List -->
        <h5 class="mb-3 mt-4">{{ app()->getLocale() === 'ar' ? 'المرضى المسجلين' : 'Registered Patients' }}</h5>
        @if($patients->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'أضيف بواسطة' : 'Added By' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->national_id }}</td>
                        <td>{{ $patient->phone }}</td>
                        <td>{{ $patient->creator->full_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا يوجد مرضى جدد' : 'No new patients' }}</p>
        @endif
    </div>
</div>
@endsection
