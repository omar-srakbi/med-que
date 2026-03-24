@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إكمال ملف المريض' : 'Complete Patient Profile')
@section('page-title', app()->getLocale() === 'ar' ? 'إكمال ملف المريض' : 'Complete Patient Profile')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-person-check"></i> {{ app()->getLocale() === 'ar' ? 'إكمال ملف المريض' : 'Complete Patient Profile' }}</h5>
        <small class="text-muted">{{ $patient->full_name }} - {{ $patient->national_id }}</small>
    </div>
    <div class="card-body">
        <form action="{{ route('patients.complete', $patient) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }}</label>
                    <input type="text" class="form-control" value="{{ $patient->first_name }}" disabled>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا يمكن تغييره' : 'Cannot be changed' }}</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Last Name' }}</label>
                    <input type="text" class="form-control" value="{{ $patient->last_name }}" disabled>
                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا يمكن تغييره' : 'Cannot be changed' }}</small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="father_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الأب' : 'Father Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('father_name') is-invalid @enderror" 
                           id="father_name" name="father_name" value="{{ old('father_name', $patient->father_name) }}" required>
                    @error('father_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="mother_name" class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الأم' : 'Mother Name' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('mother_name') is-invalid @enderror" 
                           id="mother_name" name="mother_name" value="{{ old('mother_name', $patient->mother_name) }}" required>
                    @error('mother_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="birth_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birth Date' }} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                           id="birth_date" name="birth_date" value="{{ old('birth_date', $patient->birth_date?->format('Y-m-d')) }}" required>
                    @error('birth_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="birth_place" class="form-label">{{ app()->getLocale() === 'ar' ? 'مكان الميلاد' : 'Birth Place' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('birth_place') is-invalid @enderror" 
                           id="birth_place" name="birth_place" value="{{ old('birth_place', $patient->birth_place) }}" required>
                    @error('birth_place')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="national_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'الرقم الوطني' : 'National ID' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                           id="national_id" name="national_id" value="{{ old('national_id', $patient->national_id) }}" required>
                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ وإكمال' : 'Save & Complete' }}
                </button>
                <a href="{{ route('patients.incomplete') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
