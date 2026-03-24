@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل موظف' : 'Edit Staff')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل موظف' : 'Edit Staff')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-person-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل بيانات الموظف' : 'Edit Staff Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                           id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Last Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                           id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $staff->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }} <small class="text-muted">({{ app()->getLocale() === 'ar' ? 'اتركه فارغاً للإبقاء على الحالية' : 'leave blank to keep current' }})</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password">
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
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $staff->role_id) == $role->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $role->name_ar : $role->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'يمكن تغيير دور الموظف في أي وقت' : 'Staff role can be changed at any time' }}</small>
                    @if($staff->role->name === 'Cashier')
                    <div class="alert alert-info mt-2 mb-0">
                        <small><i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'لتمكين الحجز المستقبلي، غيّر دوره إلى "أمين صندوق رئيسي" من إدارة الأدوار' : 'To enable advance booking, change their role to "Head Cashier" from Roles management' }}</small>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $staff->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3"></div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="hire_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</label>
                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                           id="hire_date" name="hire_date" value="{{ old('hire_date', $staff->hire_date?->format('Y-m-d')) }}">
                    @error('hire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="salary" class="form-label">{{ app()->getLocale() === 'ar' ? 'الراتب' : 'Salary' }}</label>
                    <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" 
                           id="salary" name="salary" value="{{ old('salary', $staff->salary) }}">
                    @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $staff->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                </button>
                <a href="{{ route('staff.show', $staff) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
