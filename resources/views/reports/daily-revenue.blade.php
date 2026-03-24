@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report')
@section('page-title', app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-stack"></i> {{ app()->getLocale() === 'ar' ? 'تقرير الإيرادات اليومي' : 'Daily Revenue Report' }}</span>
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Date Filter -->
        <form action="{{ route('reports.daily-revenue') }}" method="GET" class="mb-4">
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
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalRevenue, 2) }} JD</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalTransactions }}</h3>
                        <p>{{ app()->getLocale() === 'ar' ? 'عدد المعاملات' : 'Total Transactions' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue by Department -->
        <h5 class="mb-3">{{ app()->getLocale() === 'ar' ? 'الإيرادات حسب القسم' : 'Revenue by Department' }}</h5>
        @if($revenueByDept->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueByDept as $dept)
                    <tr>
                        <td>{{ app()->getLocale() === 'ar' ? $dept->department->name_ar : $dept->department->name }}</td>
                        <td><strong>{{ number_format($dept->total, 2) }} JD</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد إيرادات' : 'No revenue' }}</p>
        @endif
        
        <!-- Payments List -->
        <h5 class="mb-3 mt-4">{{ app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments' }}</h5>
        @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'رقم الإيصال' : 'Receipt #' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التذكرة' : 'Ticket' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->ticket->ticket_number }}</td>
                        <td>{{ $payment->ticket->patient->full_name }}</td>
                        <td>{{ $payment->cashier->full_name }}</td>
                        <td><strong>{{ number_format($payment->amount, 2) }} JD</strong></td>
                        <td>{{ $payment->created_at->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد مدفوعات' : 'No payments' }}</p>
        @endif
    </div>
</div>
@endsection
