@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'التقارير' : 'Reports')
@section('page-title', app()->getLocale() === 'ar' ? 'التقارير' : 'Reports')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'تقرير المرضى اليومي' : 'Daily Patients Report' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'عرض المرضى المسجلين والزيارات حسب القسم' : 'View registered patients and visits by department' }}</p>
                <a href="{{ route('reports.daily-patients') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-cash-stack text-success" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'عرض المدفوعات والإيرادات حسب القسم' : 'View payments and revenue by department' }}</p>
                <a href="{{ route('reports.daily-revenue') }}" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-file-earmark-medical text-info" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'سجل المريض' : 'Patient History' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'عرض تاريخ المريض والزيارات والسجلات الطبية' : 'View patient history, visits and medical records' }}</p>
                <a href="{{ route('reports.patient-history') }}" class="btn btn-info">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
