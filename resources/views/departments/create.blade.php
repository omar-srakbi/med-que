@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة قسم' : 'Add Department')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة قسم' : 'Add Department')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'بيانات القسم' : 'Department Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (إنجليزي)' : 'Department Name (English)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="name_ar" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم القسم (عربي)' : 'Department Name (Arabic)' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                           id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                    @error('name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                </button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
