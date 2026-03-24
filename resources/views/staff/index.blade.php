@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الموظفين' : 'Staff')
@section('page-title', app()->getLocale() === 'ar' ? 'الموظفين' : 'Staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge"></i> {{ app()->getLocale() === 'ar' ? 'قائمة الموظفين' : 'Staff List' }}</span>
        <a href="{{ route('staff.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة موظف' : 'Add Staff' }}
        </a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('staff.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم، البريد، الهاتف...' : 'Search by name, email, phone...' }}"
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الأدوار' : 'All Roles' }}</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $role->name_ar : $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    @if(request('search') || request('role'))
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}
                    </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Staff Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الدور' : 'Role' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ app()->getLocale() === 'ar' ? $member->role->name_ar : $member->role->name }}
                            </span>
                        </td>
                        <td>{{ $member->phone ?? '-' }}</td>
                        <td>{{ $member->hire_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            @if($member->is_active)
                                <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                            @else
                                <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('staff.show', $member) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('staff.edit', $member) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($member->id !== auth()->id())
                                <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد موظفين' : 'No staff found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $staff->links() }}
    </div>
</div>
@endsection
