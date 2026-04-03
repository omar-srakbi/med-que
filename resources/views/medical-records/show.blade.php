@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'السجل الطبي' : 'Medical Record')
@section('page-title', app()->getLocale() === 'ar' ? 'السجل الطبي' : 'Medical Record')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'معلومات السجل' : 'Record Information' }}
            </div>
            <div class="card-body">
                <p><strong>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}:</strong><br>{{ $medicalRecord->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:</strong><br>
                    <a href="{{ route('patients.show', $medicalRecord->patient) }}">{{ $medicalRecord->patient->full_name }}</a>
                </p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}:</strong><br>{{ app()->getLocale() === 'ar' ? $medicalRecord->department->name_ar : $medicalRecord->department->name }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Created By' }}:</strong><br>{{ $medicalRecord->doctor->full_name }}</p>
                @if($medicalRecord->updated_by)
                <p><strong>{{ app()->getLocale() === 'ar' ? 'آخر تعديل بواسطة' : 'Last Edited By' }}:</strong><br>
                    {{ $medicalRecord->updater->full_name }} 
                    <small class="text-muted">({{ $medicalRecord->updated_at->diffForHumans() }})</small>
                </p>
                @endif
                <p><strong>{{ app()->getLocale() === 'ar' ? 'موعد المتابعة' : 'Follow-up Date' }}:</strong><br>
                    {{ $medicalRecord->follow_up_date?->format('Y-m-d') ?? '-' }}
                </p>
                
                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                    </a>
                    <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-stethoscope"></i> {{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}
            </div>
            <div class="card-body">
                {{ $medicalRecord->diagnosis ?? app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-capsule"></i> {{ app()->getLocale() === 'ar' ? 'الوصفات الطبية' : 'Prescriptions' }}
            </div>
            <div class="card-body">
                {{ $medicalRecord->prescriptions ?? app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-activity"></i> {{ app()->getLocale() === 'ar' ? 'نتائج الفحوصات' : 'Test Results' }}
            </div>
            <div class="card-body">
                {{ $medicalRecord->test_results ?? app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-text"></i> {{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}
            </div>
            <div class="card-body">
                {{ $medicalRecord->notes ?? app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}
            </div>
        </div>
    </div>
</div>
@endsection
