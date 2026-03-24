@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل دور' : 'Edit Role')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل دور' : 'Edit Role')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-shield-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل بيانات الدور' : 'Edit Role Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الدور (إنجليزي)' : 'Role Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="name_ar" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الدور (عربي)' : 'Role Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                           id="name_ar" name="name_ar" value="{{ old('name_ar', $role->name_ar) }}" required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الصلاحيات' : 'Permissions' }}</label>
                <div class="card">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="*" id="perm_all" {{ in_array('*', $role->permissions ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="perm_all">
                                {{ app()->getLocale() === 'ar' ? 'جميع الصلاحيات' : 'All Permissions' }}
                            </label>
                            <small class="text-muted d-block">{{ app()->getLocale() === 'ar' ? 'وصول كامل للنظام' : 'Full system access' }}</small>
                        </div>
                        <hr>
                        <div class="row">
                            @foreach($availablePermissions as $perm)
                            @if($perm['value'] !== '*')
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm['value'] }}" id="perm_{{ $perm['value'] }}" {{ in_array($perm['value'], $role->permissions ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $perm['value'] }}">
                                        {{ $perm['label'] }}
                                    </label>
                                </div>
                                <small class="text-muted ms-4">{{ $perm['description'] }}</small>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('perm_all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]:not([value="*"])');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
@endsection
