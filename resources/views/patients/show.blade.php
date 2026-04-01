@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات المريض' : 'Patient Details')
@section('page-title', app()->getLocale() === 'ar' ? 'بيانات المريض' : 'Patient Details')

@section('content')
<div class="row">
    <!-- Patient Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'معلومات المريض' : 'Patient Information' }}
            </div>
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $patient->full_name }}</h5>
                <hr>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }}:</strong> {{ $patient->national_id }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birth Date' }}:</strong> {{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '-' }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'مكان الميلاد' : 'Birth Place' }}:</strong> {{ $patient->birth_place }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</strong> {{ $patient->phone }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'اسم الأب' : 'Father Name' }}:</strong> {{ $patient->father_name }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'اسم الأم' : 'Mother Name' }}:</strong> {{ $patient->mother_name }}</p>
                <hr>
                <p><small class="text-muted">{{ app()->getLocale() === 'ar' ? 'أضيف بواسطة' : 'Added by' }}: {{ $patient->creator->full_name }}</small></p>
                <p><small class="text-muted">{{ $patient->created_at->format('Y-m-d H:i') }}</small></p>
                
                <div class="mt-3">
                    @if(auth()->user()->hasPermission('manage_patients') || auth()->user()->hasPermission('create_patients'))
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                    </a>
                    @endif
                    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Medical History -->
    <div class="col-md-8">
        <!-- Visit History -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> {{ app()->getLocale() === 'ar' ? 'سجل الزيارات' : 'Visit History' }}
            </div>
            <div class="card-body">
                @if($patient->tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
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
                            @foreach($patient->tickets->take(10) as $ticket)
                            <tr>
                                <td>{{ $ticket->visit_date->format('Y-m-d') }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                                <td>{{ number_format($ticket->amount_paid, 2) }}</td>
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
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة' : 'No visits recorded' }}</p>
                @endif
            </div>
        </div>
        
        <!-- Medical Records -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'السجلات الطبية' : 'Medical Records' }}
            </div>
            <div class="card-body">
                @if($patient->medicalRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Doctor' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->medicalRecords->take(10) as $record)
                            <tr>
                                <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $record->department->name_ar : $record->department->name }}</td>
                                <td>{{ $record->doctor->full_name }}</td>
                                <td>{{ Str::limit($record->diagnosis, 50) ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد سجلات طبية' : 'No medical records' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
