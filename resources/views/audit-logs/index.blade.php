@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'سجل التدقيق' : 'Audit Logs')
@section('page-title', app()->getLocale() === 'ar' ? 'سجل التدقيق' : 'Audit Logs')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-check"></i> {{ app()->getLocale() === 'ar' ? 'سجل تدقيق النشاطات' : 'Activity Audit Logs' }}</span>
        <a href="{{ route('audit-logs.export', request()->all()) }}" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'تصدير CSV' : 'Export CSV' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('audit-logs.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Search...' }}" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل المستخدمين' : 'All Users' }}</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الإجراءات' : 'All Actions' }}</option>
                        @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'من تاريخ' : 'From Date' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'إلى تاريخ' : 'To Date' }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            @if(request()->anyFilled(['search', 'user_id', 'action', 'date_from', 'date_to']))
            <div class="mt-2">
                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء الفلتر' : 'Clear Filters' }}
                </a>
            </div>
            @endif
        </form>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النموذج' : 'Model' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'IP' : 'IP Address' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($log->user)
                                <span class="badge bg-primary">{{ $log->user->full_name }}</span>
                            @else
                                <span class="badge bg-secondary">System</span>
                            @endif
                        </td>
                        <td>
                            @if($log->action === 'created')
                                <span class="badge bg-success"><i class="bi bi-plus-circle"></i> {{ ucfirst($log->action) }}</span>
                            @elseif($log->action === 'updated')
                                <span class="badge bg-warning"><i class="bi bi-pencil"></i> {{ ucfirst($log->action) }}</span>
                            @elseif($log->action === 'deleted')
                                <span class="badge bg-danger"><i class="bi bi-trash"></i> {{ ucfirst($log->action) }}</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->model_type)
                                <span class="badge bg-secondary">{{ class_basename($log->model_type) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ Str::limit($log->description, 50) }}</td>
                        <td><small>{{ $log->ip_address }}</small></td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Log Details Modal -->
                    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ app()->getLocale() === 'ar' ? 'تفاصيل السجل' : 'Log Details' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}:</strong> {{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                                    <p><strong>{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}:</strong> {{ $log->user ? $log->user->full_name : 'System' }}</p>
                                    <p><strong>{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}:</strong> {{ ucfirst($log->action) }}</p>
                                    <p><strong>{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}:</strong> {{ $log->description }}</p>
                                    <p><strong>{{ app()->getLocale() === 'ar' ? 'IP Address' : 'IP Address' }}:</strong> {{ $log->ip_address }}</p>
                                    
                                    @if($log->old_values)
                                    <div class="mt-3">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'القيم القديمة' : 'Old Values' }}:</strong>
                                        <pre class="bg-light p-2 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    @endif
                                    
                                    @if($log->new_values)
                                    <div class="mt-3">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'القيم الجديدة' : 'New Values' }}:</strong>
                                        <pre class="bg-light p-2 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد سجلات' : 'No logs found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $logs->links() }}
    </div>
</div>
@endsection
