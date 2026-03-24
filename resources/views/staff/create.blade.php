@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة موظف' : 'Add Staff')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة موظف' : 'Add Staff')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus"></i> {{ app()->getLocale() === 'ar' ? 'بيانات الموظف' : 'Staff Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('staff.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                           id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Last Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                           id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'الدور' : 'Role' }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror"
                            id="role_id" name="role_id" required>
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدور' : 'Select Role' }}</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $role->name_ar : $role->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(auth()->user()->role->name === 'Admin')
                    <small class="text-success"><i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'يمكنك إنشاء مدير جديد' : 'You can create new Admin' }}</small>
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="hire_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</label>
                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                           id="hire_date" name="hire_date" value="{{ old('hire_date') }}">
                    @error('hire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="salary" class="form-label">{{ app()->getLocale() === 'ar' ? 'الراتب' : 'Salary' }}</label>
                    <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" 
                           id="salary" name="salary" value="{{ old('salary') }}">
                    @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                </button>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
