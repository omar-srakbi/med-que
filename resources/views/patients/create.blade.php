@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مريض' : 'Add Patient')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة مريض' : 'Add Patient')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus"></i> {{ app()->getLocale() === 'ar' ? 'بيانات المريض' : 'Patient Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('patients.store') }}" method="POST">
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
                    <label for="father_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الأب' : 'Father Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('father_name') is-invalid @enderror" 
                           id="father_name" name="father_name" value="{{ old('father_name') }}" required>
                    @error('father_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="mother_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الأم' : 'Mother Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('mother_name') is-invalid @enderror" 
                           id="mother_name" name="mother_name" value="{{ old('mother_name') }}" required>
                    @error('mother_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="birth_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birth Date' }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                    @error('birth_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="birth_place" class="form-label">{{ app()->getLocale() === 'ar' ? 'مكان الميلاد' : 'Birth Place' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('birth_place') is-invalid @enderror" 
                           id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required>
                    @error('birth_place')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="national_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                           id="national_id" name="national_id" value="{{ old('national_id') }}" required>
                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                </button>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
