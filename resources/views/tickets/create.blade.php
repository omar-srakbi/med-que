@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إنشاء تذكرة' : 'Create Ticket')
@section('page-title', app()->getLocale() === 'ar' ? 'إنشاء تذكرة' : 'Create Ticket')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'بيانات التذكرة' : 'Ticket Information' }}
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="visit_date" class="form-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الزيارة' : 'Visit Date' }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('visit_date') is-invalid @enderror"
                               id="visit_date" name="visit_date"
                               value="{{ old('visit_date', today()->format('Y-m-d')) }}"
                               min="{{ today()->format('Y-m-d') }}"
                               max="{{ now()->addDay()->format('Y-m-d') }}"
                               required>
                        @error('visit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($canBookAdvance ?? false)
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i>
                            {{ app()->getLocale() === 'ar' ? 'يمكنك الحجز لليوم أو غدٍ (كأمين صندوق رئيسي)' : 'You can book for today or tomorrow (as head cashier)' }}
                        </small>
                        @else
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'اليوم فقط' : 'Today only' }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="patient_search" class="form-label">{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }} <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" class="form-control @error('patient_id') is-invalid @enderror"
                                   id="patient_search"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب الاسم أو اختر من القائمة...' : 'Type name or select from list...' }}"
                                   autocomplete="off"
                                   value="{{ old('patient_name') }}"
                                   required>
                            <i class="bi bi-person-badge position-absolute" style="{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 10px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                            <div id="patient_results" class="position-absolute w-100 shadow-lg border-0 rounded" style="z-index: 9999; display: none; max-height: 350px; overflow-y: auto; background: white;"></div>
                        </div>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                        <input type="hidden" name="is_new_patient" id="is_new_patient" value="0">
                        @error('patient_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="mt-1">
                            <a href="{{ route('patients.create') }}" class="text-decoration-none" id="add_patient_link" style="display: none;">
                                <i class="bi bi-person-plus"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إضافة مريض جديد' : 'Add New Patient' }}</span>
                            </a>
                        </div>
                        @if($canBookAdvance ?? false)
                        <small class="text-success mt-1 d-block">
                            <i class="bi bi-lightning-charge"></i>
                            {{ app()->getLocale() === 'ar' ? 'اختر من القائمة أو اكتب اسماً جديداً - سيتم إنشاء المريض تلقائياً' : 'Select from list or type new name - patient will be auto-created' }}
                        </small>
                        @else
                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-info-circle"></i>
                            {{ app()->getLocale() === 'ar' ? 'اختر من القائمة أو اكتب اسماً جديداً' : 'Select from list or type new name' }}
                        </small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="quick_select" class="form-label">
                            <i class="bi bi-keyboard"></i> {{ app()->getLocale() === 'ar' ? 'اختصار سريع' : 'Quick Select' }}
                            <small class="text-muted">({{ app()->getLocale() === 'ar' ? 'مثال: 1 أو 2.3' : 'e.g., 1 or 2.3' }})</small>
                        </label>
                        <input type="text" class="form-control" 
                               id="quick_select" 
                               placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب رقم العيادة.الخدمة (مثال: 1.2)' : 'Enter clinic.service (e.g., 1.2)' }}"
                               autocomplete="off">
                        <small class="text-muted">
                            {{ app()->getLocale() === 'ar' ? '1 = العيادة الأولى، الخدمة الأولى | 1.2 = العيادة الأولى، الخدمة الثانية' : '1 = First clinic, first service | 1.2 = First clinic, second service' }}
                        </small>
                        <div id="quick_select_result" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('department_id') is-invalid @enderror"
                                id="department_id" name="department_id" required>
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر القسم' : 'Select Department' }}</option>
                            @foreach($departments as $index => $department)
                            <option value="{{ $department->id }}" data-index="{{ $index + 1 }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $index + 1 }}. {{ app()->getLocale() === 'ar' ? $department->name_ar : $department->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($errors->has('department'))
                            <div class="alert alert-danger mt-2 mb-0">
                                <i class="bi bi-exclamation-triangle"></i> {{ $errors->first('department') }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="service_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_id') is-invalid @enderror"
                                id="service_id" name="service_id" required>
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الخدمة' : 'Select Service' }}</option>
                        </select>
                        <div id="service_price" class="mt-2" style="display: none;">
                            <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}: <span id="price_value">0</span></span>
                        </div>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info" id="price-info" style="display: none;">
                        <strong>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}:</strong>
                        <span id="service-price">0</span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" id="submit_ticket" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'إنشاء ودفع' : 'Create & Pay' }}
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('tickets.create-scripts')
@endpush
