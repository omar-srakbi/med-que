@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إيصال التذكرة' : 'Ticket Receipt')
@section('page-title', app()->getLocale() === 'ar' ? 'إيصال التذكرة' : 'Ticket Receipt')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card" id="receipt-card">
            <div class="card-header text-center">
                <h4><i class="bi bi-hospital"></i> {{ app()->getLocale() === 'ar' ? 'المركز الطبي' : 'Medical Center' }}</h4>
                <p class="mb-0 text-muted">{{ app()->getLocale() === 'ar' ? 'إيصال تذكرة' : 'Ticket Receipt' }}</p>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="text-primary">{{ $ticket->ticket_number }}</h2>
                    <p class="text-muted">{{ $ticket->visit_date->format('Y-m-d H:i') }}</p>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:</strong></td>
                        <td class="text-end">{{ $ticket->patient->full_name }}</td>
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
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'رقم الإيصال' : 'Receipt Number' }}:</strong></td>
                        <td class="text-end">{{ $ticket->payment->receipt_number }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}:</strong></td>
                        <td class="text-end">{{ $ticket->cashier->full_name }}</td>
                    </tr>
                </table>
                
                <div class="alert alert-warning text-center mt-4">
                    <i class="bi bi-info-circle"></i>
                    {{ app()->getLocale() === 'ar' ? 'يرجى الانتظار حتى يتم استدعاء رقمك' : 'Please wait until your number is called' }}
                </div>
            </div>
            <div class="card-footer text-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
                </button>
                <a href="{{ route('tickets.create') }}" class="btn btn-secondary">
                    <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'تذكرة جديدة' : 'New Ticket' }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .btn, .main-content > div:first-child {
        display: none !important;
    }
    #receipt-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
@endsection
