@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'التذاكر' : 'Tickets')
@section('page-title', app()->getLocale() === 'ar' ? 'التذاكر' : 'Tickets')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'قائمة التذاكر' : 'Ticket List' }}</span>
        @can('create_tickets')
        <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'تذكرة جديدة' : 'New Ticket' }}
        </a>
        @endcan
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="{{ route('tickets.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="date" name="date" class="form-control" value="{{ request('date', today()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <select name="department" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الأقسام' : 'All Departments' }}</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if(request('date') || request('department'))
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tickets Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الطابور' : 'Queue #' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><span class="badge bg-primary">{{ $ticket->ticket_number }}</span></td>
                        <td>{{ $ticket->patient->full_name }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}</td>
                        <td>{{ app()->getLocale() === 'ar' ? $ticket->service->name_ar : $ticket->service->name }}</td>
                        <td><span class="badge bg-info">{{ $ticket->queue_number }}</span></td>
                        <td>{{ \App\Models\Setting::formatCurrency($ticket->amount_paid) }}</td>
                        <td>{{ $ticket->created_at_time?->format('H:i') }}</td>
                        <td>
                            @if($ticket->completed_at)
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'مكتمل' : 'Completed' }}</span>
                            @else
                                <span class="badge bg-warning">{{ app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'Pending' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!$ticket->completed_at)
                                <form action="{{ route('tickets.complete', $ticket) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if(auth()->user()->role->name === 'Admin' || auth()->user()->hasPermission('delete_tickets'))
                                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            @if(auth()->user()->role->name !== 'Admin' && $ticket->completed_at) disabled title="{{ app()->getLocale() === 'ar' ? 'لا يمكن حذف التذكرة المكتملة' : 'Cannot delete completed ticket' }}" @endif>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد تذاكر' : 'No tickets found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $tickets->links() }}
    </div>
</div>
@endsection
