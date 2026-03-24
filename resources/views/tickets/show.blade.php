@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات التذكرة' : 'Ticket Details')
@section('page-title', app()->getLocale() === 'ar' ? 'بيانات التذكرة' : 'Ticket Details')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'معلومات التذكرة' : 'Ticket Information' }}
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="text-primary">{{ $ticket->ticket_number }}</h2>
                    <p class="text-muted">{{ $ticket->visit_date->format('Y-m-d H:i') }}</p>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:</strong></td>
                        <td class="text-end">
                            <a href="{{ route('patients.show', $ticket->patient) }}">{{ $ticket->patient->full_name }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}:</strong></td>
                        <td class="text-end">{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}:</strong></td>
                        <td class="text-end">{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue Number' }}:</strong></td>
                        <td class="text-end"><span class="badge bg-info fs-6">{{ $ticket->queue_number }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'المبلغ المدفوع' : 'Amount Paid' }}:</strong></td>
                        <td class="text-end"><span class="text-success fw-bold">{{ number_format($ticket->amount_paid, 2) }} JD</span></td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}:</strong></td>
                        <td class="text-end">{{ $ticket->cashier->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}:</strong></td>
                        <td class="text-end">
                            @if($ticket->completed_at)
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}</span>
                            @else
                                <span class="badge bg-warning">{{ app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
                
                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('tickets.receipt', $ticket) }}" class="btn btn-success" target="_blank">
                        <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة الإيصال' : 'Print Receipt' }}
                    </a>
                    @if(!$ticket->completed_at)
                    <form action="{{ route('tickets.complete', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'إكمال التذكرة' : 'Complete Ticket' }}
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        @if($ticket->medicalRecord)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'السجل الطبي' : 'Medical Record' }}
            </div>
            <div class="card-body">
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Doctor' }}:</strong> {{ $ticket->medicalRecord->doctor->full_name }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}:</strong><br>{{ $ticket->medicalRecord->diagnosis ?? '-' }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الوصفات' : 'Prescriptions' }}:</strong><br>{{ $ticket->medicalRecord->prescriptions ?? '-' }}</p>
                <a href="{{ route('medical-records.show', $ticket->medicalRecord) }}" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'عرض السجل' : 'View Record' }}
                </a>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'ملاحظة' : 'Note' }}
            </div>
            <div class="card-body text-center text-muted">
                {{ app()->getLocale() === 'ar' ? 'لا يوجد سجل طبي مرتبط بهذه التذكرة' : 'No medical record associated with this ticket' }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
