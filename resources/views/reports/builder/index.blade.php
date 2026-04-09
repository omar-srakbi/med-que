@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'منشئ التقارير' : 'Report Builder')
@section('page-title', app()->getLocale() === 'ar' ? 'منشئ التقارير' : 'Report Builder')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-bar-graph"></i> {{ app()->getLocale() === 'ar' ? 'تقاريري المخصصة' : 'My Custom Reports' }}</span>
        <a href="{{ route('reports.builder.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> {{ app()->getLocale() === 'ar' ? 'تقرير جديد' : 'New Report' }}
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($reports->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المصدر' : 'Source' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تم الإنشاء بواسطة' : 'Created By' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'عام' : 'Public' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الإنشاء' : 'Created' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        <td>
                            <strong>{{ app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en }}</strong>
                        </td>
                        <td>
                            @if($report->report_type === 'simple')
                                <span class="badge bg-info">{{ app()->getLocale() === 'ar' ? 'بسيط' : 'Simple' }}</span>
                            @else
                                <span class="badge bg-purple">{{ app()->getLocale() === 'ar' ? 'متقدم' : 'Advanced' }}</span>
                            @endif
                        </td>
                        <td>{{ $report->data_source }}</td>
                        <td>{{ $report->creator->full_name ?? 'System' }}</td>
                        <td>
                            @if($report->is_public)
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نعم' : 'Yes' }}</span>
                            @else
                                <span class="badge bg-secondary">{{ app()->getLocale() === 'ar' ? 'لا' : 'No' }}</span>
                            @endif
                        </td>
                        <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reports.builder.show', $report) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('reports.builder.permissions', $report) }}" class="btn btn-outline-info" title="{{ app()->getLocale() === 'ar' ? 'إدارة الصلاحيات' : 'Manage Permissions' }}">
                                    <i class="bi bi-shield-lock"></i>
                                </a>
                                @can('edit', $report)
                                <a href="{{ route('reports.builder.edit', $report) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $report)
                                <form action="{{ route('reports.builder.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $reports->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-bar-graph" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3">{{ app()->getLocale() === 'ar' ? 'لا توجد تقارير مخصصة' : 'No custom reports yet' }}</h5>
            <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'أنشئ تقريرك الأول الآن!' : 'Create your first custom report!' }}</p>
            <a href="{{ route('reports.builder.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> {{ app()->getLocale() === 'ar' ? 'تقرير جديد' : 'New Report' }}
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
