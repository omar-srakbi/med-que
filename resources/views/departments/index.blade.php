@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments')
@section('page-title', app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'قائمة الأقسام' : 'Department List' }}</span>
        <a href="{{ route('departments.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة قسم' : 'Add Department' }}
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($departments as $department)
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ app()->getLocale() === 'ar' ? $department->name_ar : $department->name }}</h5>
                        @if(!$department->is_active)
                            <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">{{ $department->description ?? '-' }}</p>
                        <p class="mb-1"><strong>{{ app()->getLocale() === 'ar' ? 'الخدمات' : 'Services' }}:</strong> {{ $department->services_count }}</p>
                        
                        @if($department->services->count() > 0)
                        <div class="mb-3">
                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'أسعار الخدمات:' : 'Service Prices:' }}</small>
                            <ul class="list-unstyled mb-0 small">
                                @foreach($department->services->take(3) as $service)
                                <li>{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name }} - {{ number_format($service->price, 2) }} JD</li>
                                @endforeach
                                @if($department->services->count() > 3)
                                <li class="text-muted">+ {{ $department->services->count() - 3 }} {{ app()->getLocale() === 'ar' ? 'أخرى' : 'more' }}</li>
                                @endif
                            </ul>
                        </div>
                        @endif
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('departments.show', $department) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                            </a>
                            <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center py-4">{{ app()->getLocale() === 'ar' ? 'لا توجد أقسام' : 'No departments found' }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
