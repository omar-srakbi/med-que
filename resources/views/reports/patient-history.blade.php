@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'سجل المريض' : 'Patient History')
@section('page-title', app()->getLocale() === 'ar' ? 'سجل المريض' : 'Patient History')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'سجل المريض' : 'Patient History' }}</span>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Patient Selection -->
        <form action="{{ route('reports.patient-history') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اختر المريض' : 'Select Patient' }}</label>
                    <select name="patient_id" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المريض' : 'Select Patient' }}</option>
                        @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ request('patient_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->full_name }} - {{ $p->national_id }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                    </button>
                </div>
            </div>
        </form>
        
        @if($patient)
        <!-- Patient Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ app()->getLocale() === 'ar' ? 'معلومات المريض' : 'Patient Information' }}</div>
                    <div class="card-body">
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}:</strong> {{ $patient->full_name }}</p>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}:</strong> {{ $patient->national_id }}</p>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birth Date' }}:</strong> {{ $patient->birth_date->format('Y-m-d') }}</p>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</strong> {{ $patient->phone }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ app()->getLocale() === 'ar' ? 'إحصائيات' : 'Statistics' }}</div>
                    <div class="card-body">
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'عدد الزيارات' : 'Total Visits' }}:</strong> {{ $tickets->count() }}</p>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records' }}:</strong> {{ $medicalRecords->count() }}</p>
                        <p><strong>{{ app()->getLocale() === 'ar' ? 'آخر زيارة' : 'Last Visit' }}:</strong> 
                            {{ $tickets->first()?->visit_date?->format('Y-m-d') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visit History -->
        <h5 class="mb-3">{{ app()->getLocale() === 'ar' ? 'سجل الزيارات' : 'Visit History' }}</h5>
        @if($tickets->count() > 0)
        <div class="table-responsive mb-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->visit_date->format('Y-m-d') }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                        <td>{{ \App\Models\Setting::formatCurrency($ticket->amount_paid) }}</td>
                        <td>
                            @if($ticket->completed_at)
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}</span>
                            @else
                                <span class="badge bg-warning">{{ app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center mb-4">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات' : 'No visits' }}</p>
        @endif
        
        <!-- Medical Records -->
        <h5 class="mb-3">{{ app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records' }}</h5>
        @if($medicalRecords->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Doctor' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'موعد المتابعة' : 'Follow-up' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicalRecords as $record)
                    <tr>
                        <td>{{ $record->created_at->format('Y-m-d') }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $record->department->name_ar : $record->department->name }}</td>
                        <td>{{ $record->doctor->full_name }}</td>
                        <td>{{ Str::limit($record->diagnosis, 50) ?? '-' }}</td>
                        <td>{{ $record->follow_up_date?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد سجلات طبية' : 'No medical records' }}</p>
        @endif
        
        @else
        <p class="text-muted text-center py-4">{{ app()->getLocale() === 'ar' ? 'اختر مريض لعرض السجل' : 'Select a patient to view history' }}</p>
        @endif
    </div>
</div>
@endsection
