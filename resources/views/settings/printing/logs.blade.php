@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'سجل الطباعة' : 'Print Logs')
@section('page-title', app()->getLocale() === 'ar' ? 'سجل الطباعة' : 'Print Logs')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text"></i> {{ app()->getLocale() === 'ar' ? 'سجل الطباعة' : 'Print Logs' }}</span>
        <a href="{{ route('settings.printing.logs.export') }}" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'تصدير CSV' : 'Export CSV' }}
        </a>
    </div>
    <div class="card-body">
        @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'السجل' : 'Record' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الطابعة' : 'Printer' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النسخ' : 'Copies' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->printed_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $log->user ? $log->user->full_name : 'System' }}</td>
                        <td>
                            @if($log->print_type === 'ticket')
                                <span class="badge bg-primary">{{ app()->getLocale() === 'ar' ? 'تذكرة' : 'Ticket' }}</span>
                            @elseif($log->print_type === 'receipt')
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'إيصال' : 'Receipt' }}</span>
                            @elseif($log->print_type === 'medical_record')
                                <span class="badge bg-info">{{ app()->getLocale() === 'ar' ? 'سجل طبي' : 'Medical Record' }}</span>
                            @else
                                {{ ucfirst($log->print_type) }}
                            @endif
                        </td>
                        <td>{{ $log->record_type }} #{{ $log->record_id }}</td>
                        <td>{{ $log->printer_name ?? 'Default' }}</td>
                        <td>{{ $log->copies }}</td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'ناجح' : 'Success' }}</span>
                            @else
                                <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'فشل' : 'Failed' }}</span>
                                @if($log->error_message)
                                    <br><small class="text-muted">{{ Str::limit($log->error_message, 50) }}</small>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $logs->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد سجلات طباعة' : 'No print logs available' }}</h5>
            <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'سجلات الطباعة ستظهر هنا' : 'Print logs will appear here' }}</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('settings.printing.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع للإعدادات' : 'Back to Settings' }}
    </a>
</div>
@endsection
