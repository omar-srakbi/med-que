@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'التقارير' : 'Reports')
@section('page-title', app()->getLocale() === 'ar' ? 'التقارير' : 'Reports')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4><i class="bi bi-bar-chart"></i> {{ app()->getLocale() === 'ar' ? 'التقارير' : 'Reports' }}</h4>
            <a href="{{ route('reports.builder.index') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-tools"></i> {{ app()->getLocale() === 'ar' ? 'منشئ التقارير المخصصة' : 'Custom Report Builder' }}
            </a>
        </div>
    </div>

    <div class="col-12 mb-4">
        <h4><i class="bi bi-bar-chart"></i> {{ app()->getLocale() === 'ar' ? 'التقارير المالية' : 'Financial Reports' }}</h4>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check text-primary" style="font-size: 3rem;"></i>
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
                <i class="bi bi-calendar3 text-info" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات الشهري' : 'Monthly Revenue Report' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'عرض الإيرادات الشهرية مع مقارنة السنوات' : 'View monthly revenue with year-over-year comparison' }}</p>
                <a href="{{ route('reports.monthly-revenue') }}" class="btn btn-info">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4 mt-4">
        <h4><i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'تقارير المرضى' : 'Patient Reports' }}</h4>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-file-earmark-medical text-warning" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'سجل المريض' : 'Patient History' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'عرض تاريخ المريض والزيارات والسجلات الطبية' : 'View patient history, visits and medical records' }}</p>
                <a href="{{ route('reports.patient-history') }}" class="btn btn-warning">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-pie-chart text-secondary" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'التركيبة السكانية' : 'Patient Demographics' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'توزيع الأعمار والمناطق الجغرافية' : 'Age and geographic distribution analysis' }}</p>
                <a href="{{ route('reports.patient-demographics') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-arrow-repeat text-danger" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'تكرار الزيارات' : 'Visit Frequency' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'تحليل المرضى العائدين والزيارات المتكررة' : 'Analyze returning patients and frequent visits' }}</p>
                <a href="{{ route('reports.visit-frequency') }}" class="btn btn-danger">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4 mt-4">
        <h4><i class="bi bi-graph-up"></i> {{ app()->getLocale() === 'ar' ? 'تقارير الأداء' : 'Performance Reports' }}</h4>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-building text-primary" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'أداء الأقسام' : 'Department Performance' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'تحليل الزيارات والإيرادات حسب القسم' : 'Analyze visits and revenue by department' }}</p>
                <a href="{{ route('reports.department-performance') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-badge text-success" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'أداء الأمناء' : 'Cashier Performance' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'تحليل معاملات وأداء كل أمين' : 'Analyze transactions and performance per cashier' }}</p>
                <a href="{{ route('reports.cashier-performance') }}" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-heart-pulse text-info" style="font-size: 3rem;"></i>
                <h5 class="card-title mt-3">{{ app()->getLocale() === 'ar' ? 'أداء الخدمات' : 'Services Report' }}</h5>
                <p class="card-text">{{ app()->getLocale() === 'ar' ? 'تحليل الخدمات الأكثر طلباً والإيرادات' : 'Analyze most requested services and revenue' }}</p>
                <a href="{{ route('reports.services') }}" class="btn btn-info">
                    <i class="bi bi-arrow-right"></i> {{ app()->getLocale() === 'ar' ? 'عرض التقرير' : 'View Report' }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
