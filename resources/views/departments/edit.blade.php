@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل قسم' : 'Edit Department')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل قسم' : 'Edit Department')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'تعديل بيانات القسم' : 'Edit Department Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('departments.update', $department) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (إنجليزي)' : 'Department Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $department->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name_ar" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (عربي)' : 'Department Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                           id="name_ar" name="name_ar" value="{{ old('name_ar', $department->name_ar) }}" required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sequence_prefix" class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة التذكرة' : 'Ticket Prefix' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('sequence_prefix') is-invalid @enderror"
                           id="sequence_prefix" name="sequence_prefix" value="{{ old('sequence_prefix', $department->sequence_prefix) }}" maxlength="2" required>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان - مشترك بين الأقسام' : '2 chars - shared among depts' }}</small>
                    @error('sequence_prefix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="queue_prefix" class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة الطابور' : 'Queue Prefix' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('queue_prefix') is-invalid @enderror"
                           id="queue_prefix" name="queue_prefix" value="{{ old('queue_prefix', $department->queue_prefix) }}" maxlength="2" required>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان - فريد لكل قسم' : '2 chars - unique per dept' }}</small>
                    @error('queue_prefix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
                    </label>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                </button>
                <a href="{{ route('departments.show', $department) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
