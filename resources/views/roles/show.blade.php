@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات الدور' : 'Role Details')
@section('page-title', app()->getLocale() === 'ar' ? 'بيانات الدور' : 'Role Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shield-lock"></i> {{ app()->getLocale() === 'ar' ? 'معلومات الدور' : 'Role Information' }}
            </div>
            <div class="card-body">
                <h4>{{ $role->name }}</h4>
                <p class="text-muted">{{ $role->name_ar }}</p>
                <hr>
                <p>
                    <strong>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}:</strong><br>
                    @if($role->is_system)
                        <span class="badge bg-secondary"><i class="bi bi-lock"></i> {{ app()->getLocale() === 'ar' ? 'دور نظام' : 'System Role' }}</span>
                    @else
                        <span class="badge bg-info"><i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'دور مخصص' : 'Custom Role' }}</span>
                    @endif
                </p>
                <p>
                    <strong>{{ app()->getLocale() === 'ar' ? 'عدد الموظفين' : 'Staff Count' }}:</strong><br>
                    <span class="badge bg-primary">{{ $role->users_count }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'staff' }}</span>
                </p>
                <p>
                    <strong>{{ app()->getLocale() === 'ar' ? 'الصلاحيات' : 'Permissions' }}:</strong><br>
                    @if($role->permissions && count($role->permissions) > 0)
                        @if(in_array('*', $role->permissions))
                            <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'جميع الصلاحيات' : 'All Permissions' }}</span>
                        @else
                            @foreach($role->permissions as $permission)
                                <span class="badge bg-warning me-1 mb-1">{{ $permission }}</span>
                            @endforeach
                        @endif
                    @else
                        <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}</span>
                    @endif
                </p>
                
                <div class="mt-3 d-grid gap-2">
                    @if(!$role->is_system)
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                    </a>
                    @endif
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'الموظفين بهذا الدور' : 'Staff with this Role' }}
            </div>
            <div class="card-body">
                @if($role->users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                            <tr>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا يوجد موظفين بهذا الدور' : 'No staff assigned to this role' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
