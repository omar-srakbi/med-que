@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل سجل طبي' : 'Edit Medical Record')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل سجل طبي' : 'Edit Medical Record')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'تعديل السجل الطبي' : 'Edit Medical Record' }}
    </div>
    <div class="card-body">
        <form action="{{ route('medical-records.update', $medicalRecord) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="patient_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('patient_id') is-invalid @enderror" 
                            id="patient_id" name="patient_id" required>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id', $medicalRecord->patient_id) == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} - {{ $patient->national_id }}
                        </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('department_id') is-invalid @enderror" 
                            id="department_id" name="department_id" required>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $medicalRecord->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="follow_up_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'موعد المتابعة' : 'Follow-up Date' }}</label>
                <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" 
                       id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date', $medicalRecord->follow_up_date?->format('Y-m-d')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                @error('follow_up_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="diagnosis" class="form-label">{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}</label>
                <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                          id="diagnosis" name="diagnosis" rows="3">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                @error('diagnosis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="prescriptions" class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصفات الطبية' : 'Prescriptions' }}</label>
                <textarea class="form-control @error('prescriptions') is-invalid @enderror" 
                          id="prescriptions" name="prescriptions" rows="3">{{ old('prescriptions', $medicalRecord->prescriptions) }}</textarea>
                @error('prescriptions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="test_results" class="form-label">{{ app()->getLocale() === 'ar' ? 'نتائج الفحوصات' : 'Test Results' }}</label>
                <textarea class="form-control @error('test_results') is-invalid @enderror" 
                          id="test_results" name="test_results" rows="3">{{ old('test_results', $medicalRecord->test_results) }}</textarea>
                @error('test_results')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">{{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="2">{{ old('notes', $medicalRecord->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                </button>
                <a href="{{ route('medical-records.show', $medicalRecord) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
