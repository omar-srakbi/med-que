@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard')

@section('page-title', app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'إجمالي المرضى' : 'Total Patients' }}</h6>
                        <h3 class="mb-0">{{ $stats['total_patients'] }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'مرضى اليوم' : "Today's Patients" }}</h6>
                        <h3 class="mb-0">{{ $stats['today_patients'] }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-person-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'تذاكر اليوم' : "Today's Tickets" }}</h6>
                        <h3 class="mb-0">{{ $stats['today_tickets'] }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-ticket-perforated" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">{{ app()->getLocale() === 'ar' ? 'إيرادات اليوم' : "Today's Revenue" }}</h6>
                        <h3 class="mb-0">{{ number_format($stats['today_revenue'], 2) }}</h3>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'د.أ' : 'JD' }}</small>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tickets -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> {{ app()->getLocale() === 'ar' ? 'آخر التذاكر اليوم' : "Today's Recent Tickets" }}</span>
                @can('create_tickets')
                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة تذكرة' : 'New Ticket' }}
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($recentTickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue #' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTickets as $ticket)
                            <tr>
                                <td><span class="badge bg-primary">{{ $ticket->ticket_number }}</span></td>
                                <td>{{ $ticket->patient->full_name }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                                <td><span class="badge bg-info">{{ $ticket->queue_number }}</span></td>
                                <td>{{ number_format($ticket->amount_paid, 2) }}</td>
                                <td>{{ $ticket->created_at_time?->format('H:i') }}</td>
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
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد تذاكر اليوم' : 'No tickets today' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
