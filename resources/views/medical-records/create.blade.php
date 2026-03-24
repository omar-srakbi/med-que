@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة سجل طبي' : 'Add Medical Record')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة سجل طبي' : 'Add Medical Record')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-medical"></i> {{ app()->getLocale() === 'ar' ? 'بيانات السجل الطبي' : 'Medical Record Information' }}
    </div>
    <div class="card-body">
        <form action="{{ route('medical-records.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="patient_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('patient_id') is-invalid @enderror" 
                            id="patient_id" name="patient_id" required>
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المريض' : 'Select Patient' }}</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} - {{ $patient->national_id }}
                        </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ticket_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'التذكرة' : 'Ticket' }} <small class="text-muted">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</small></label>
                    <select class="form-select @error('ticket_id') is-invalid @enderror" 
                            id="ticket_id" name="ticket_id">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر التذكرة' : 'Select Ticket' }}</option>
                        @foreach($todayTickets as $ticket)
                        <option value="{{ $ticket->id }}" {{ old('ticket_id') == $ticket->id ? 'selected' : '' }}>
                            {{ $ticket->ticket_number }} - {{ $ticket->patient->full_name }} - {{ app()->getLocale() === 'ar' ? $ticket->department->name_ar : $ticket->department->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('ticket_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('department_id') is-invalid @enderror" 
                            id="department_id" name="department_id" required>
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر القسم' : 'Select Department' }}</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $dept->name_ar : $dept->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="follow_up_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'موعد المتابعة' : 'Follow-up Date' }}</label>
                    <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" 
                           id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('follow_up_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="diagnosis" class="form-label">{{ app()->getLocale() === 'ar' ? 'التشخيص' : 'Diagnosis' }}</label>
                <textarea class="form-control @error('diagnosis') is-invalid @enderror" 
                          id="diagnosis" name="diagnosis" rows="3">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="prescriptions" class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصفات الطبية' : 'Prescriptions' }}</label>
                <textarea class="form-control @error('prescriptions') is-invalid @enderror" 
                          id="prescriptions" name="prescriptions" rows="3">{{ old('prescriptions') }}</textarea>
                @error('prescriptions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="test_results" class="form-label">{{ app()->getLocale() === 'ar' ? 'نتائج الفحوصات' : 'Test Results' }}</label>
                <textarea class="form-control @error('test_results') is-invalid @enderror" 
                          id="test_results" name="test_results" rows="3">{{ old('test_results') }}</textarea>
                @error('test_results')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">{{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                </button>
                <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
