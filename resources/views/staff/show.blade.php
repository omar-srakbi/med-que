@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات الموظف' : 'Staff Details')
@section('page-title', app()->getLocale() === 'ar' ? 'بيانات الموظف' : 'Staff Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge"></i> {{ app()->getLocale() === 'ar' ? 'معلومات الموظف' : 'Staff Information' }}
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                    </div>
                </div>
                <h5 class="text-center">{{ $staff->full_name }}</h5>
                <p class="text-center text-muted">{{ app()->getLocale() === 'ar' ? $staff->role->name_ar : $staff->role->name }}</p>
                <hr>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}:</strong><br>{{ $staff->email }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</strong><br>{{ $staff->phone ?? '-' }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}:</strong><br>{{ $staff->hire_date?->format('Y-m-d') ?? '-' }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الراتب' : 'Salary' }}:</strong><br>{{ $staff->salary ? number_format($staff->salary, 2) : '-' }} JD</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}:</strong><br>
                    @if($staff->is_active)
                        <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                    @else
                        <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                    @endif
                </p>
                <hr>
                <p><small class="text-muted">{{ app()->getLocale() === 'ar' ? 'أضيف في' : 'Added on' }}: {{ $staff->created_at->format('Y-m-d H:i') }}</small></p>
                
                <div class="mt-3 d-grid gap-2">
                    <a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                    </a>
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        @if($staff->role->name === 'Cashier')
        <!-- Cashier Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> {{ app()->getLocale() === 'ar' ? 'إحصائيات اليوم' : "Today's Stats" }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $staff->cashierTickets()->whereDate('visit_date', today())->count() }}</h3>
                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'تذكرة اليوم' : "Today's Tickets" }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="text-success">{{ number_format($staff->payments()->whereDate('created_at', today())->sum('amount'), 2) }}</h3>
                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'إيرادات اليوم' : "Today's Revenue" }} (JD)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> {{ app()->getLocale() === 'ar' ? 'آخر النشاطات' : 'Recent Activity' }}
            </div>
            <div class="card-body">
                @if($staff->role->name === 'Cashier')
                <h6>{{ app()->getLocale() === 'ar' ? 'آخر التذاكر' : 'Recent Tickets' }}</h6>
                @if($staff->cashierTickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff->cashierTickets->take(5) as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_number }}</td>
                                <td>{{ $ticket->patient->full_name }}</td>
                                <td>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                                <td>{{ number_format($ticket->amount_paid, 2) }}</td>
                                <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد تذاكر' : 'No tickets' }}</p>
                @endif
                @else
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد نشاطات لعرضها' : 'No activity to display' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
