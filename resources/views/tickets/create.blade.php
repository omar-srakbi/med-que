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
                        @if($canBookAdvance)
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i>
                            {{ app()->getLocale() === 'ar' ? 'يمكنك الحجز لليوم أو غدٍ (كأمين صندوق رئيسي)' : 'You can book for today or tomorrow (as head cashier)' }}
                        </small>
                        @else
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'اليوم فقط' : 'Today only' }}</small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
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
                        <div class="form-text">
                            <a href="{{ route('patients.create') }}" target="_blank">
                                <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة مريض جديد' : 'Add New Patient' }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="department_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                id="department_id" name="department_id" required>
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر القسم' : 'Select Department' }}</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? $department->name_ar : $department->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_id" class="form-label">{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_id') is-invalid @enderror" 
                                id="service_id" name="service_id" required>
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الخدمة' : 'Select Service' }}</option>
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info" id="price-info" style="display: none;">
                        <strong>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}:</strong> 
                        <span id="service-price">0</span> {{ app()->getLocale() === 'ar' ? 'د.أ' : 'JD' }}
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
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

@push('scripts')
<script>
const isArabic = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_id');
    const serviceSelect = document.getElementById('service_id');
    const priceInfo = document.getElementById('price-info');
    const servicePrice = document.getElementById('service-price');
    
    // Load services when department changes
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        if (departmentId) {
            fetch(`/api/departments/${departmentId}/services`)
                .then(response => response.json())
                .then(data => {
                    serviceSelect.innerHTML = '<option value="">'+(isArabic ? 'اختر الخدمة' : 'Select Service')+'</option>';
                    data.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.id;
                        option.textContent = (isArabic ? service.name_ar : service.name) + ' - ' + service.price + ' JD';
                        option.dataset.price = service.price;
                        serviceSelect.appendChild(option);
                    });
                });
        } else {
            serviceSelect.innerHTML = '<option value="">'+(isArabic ? 'اختر الخدمة' : 'Select Service')+'</option>';
            priceInfo.style.display = 'none';
        }
    });
    
    // Show price when service changes
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value && selectedOption.dataset.price) {
            servicePrice.textContent = selectedOption.dataset.price;
            priceInfo.style.display = 'block';
        } else {
            priceInfo.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection
