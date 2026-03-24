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
                                   list="patient_list"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب الاسم أو اختر من القائمة...' : 'Type name or select from list...' }}"
                                   autocomplete="off"
                                   value="{{ old('patient_name') }}"
                                   required>
                            <datalist id="patient_list">
                                @foreach($patients ?? [] as $patient)
                                <option value="{{ $patient->full_name }}" data-id="{{ $patient->id }}" data-national-id="{{ $patient->national_id }}">
                                @endforeach
                            </datalist>
                            <i class="bi bi-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        </div>
                        <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                        <input type="hidden" name="is_new_patient" id="is_new_patient" value="0">
                        <div id="patient_results" class="list-group position-absolute w-100 shadow border" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                        @error('patient_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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

    // Simple patient selection with datalist
    const patientSearch = document.getElementById('patient_search');
    const patientIdField = document.getElementById('patient_id');
    const patientDatalist = document.getElementById('patient_list');
    let selectedPatientId = null;

    if (patientSearch && patientDatalist) {
        // When user types, check if it matches a datalist option
        patientSearch.addEventListener('change', function() {
            const query = this.value.trim();
            const options = patientDatalist.options;
            
            // Search for matching option
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.toLowerCase() === query.toLowerCase()) {
                    // Found match - set patient ID
                    selectedPatientId = options[i].dataset.id;
                    patientIdField.value = selectedPatientId;
                    console.log('✓ Selected from list:', query, 'ID:', selectedPatientId);
                    return;
                }
            }
            
            // No match - clear ID (will create new patient)
            selectedPatientId = null;
            patientIdField.value = '';
            console.log('✗ No match, will create new:', query);
        });

        // Also check on blur
        patientSearch.addEventListener('blur', function() {
            const query = this.value.trim();
            const options = patientDatalist.options;
            
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.toLowerCase() === query.toLowerCase()) {
                    selectedPatientId = options[i].dataset.id;
                    patientIdField.value = selectedPatientId;
                    return;
                }
            }
        });

        // Form submit handler
        const form = patientSearch.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const query = patientSearch.value.trim();
                
                if (!query) {
                    e.preventDefault();
                    patientSearch.classList.add('is-invalid');
                    
                    let errorDiv = patientSearch.parentNode.querySelector('.invalid-feedback');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        patientSearch.parentNode.insertBefore(errorDiv, patientSearch.nextSibling);
                    }
                    errorDiv.textContent = document.documentElement.lang === 'ar' ? 'يرجى اختيار مريض أولاً' : 'Please select a patient first';
                    patientSearch.focus();
                    return;
                }

                // If we have a selected patient ID, use it
                if (selectedPatientId) {
                    patientIdField.value = selectedPatientId;
                    console.log('✓ Submitting with existing patient:', selectedPatientId);
                    return;
                }

                // Check one more time if this matches any datalist option
                const options = patientDatalist.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value.toLowerCase() === query.toLowerCase()) {
                        patientIdField.value = options[i].dataset.id;
                        console.log('✓ Found on submit:', options[i].dataset.id);
                        return;
                    }
                }

                // No match - this is a new patient, create via AJAX
                e.preventDefault();
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating patient...';
                
                fetch(`/api/patients/search-or-create?q=${encodeURIComponent(query)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'HTTP error ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Patient response:', data);
                    
                    if (data.action === 'created' || data.action === 'found') {
                        patientIdField.value = data.patient.id;
                        
                        // Show message
                        const existingAlert = patientSearch.parentNode.querySelector('.alert-success, .alert-info');
                        if (existingAlert) existingAlert.remove();
                        
                        const alertClass = data.action === 'created' ? 'alert-success' : 'alert-info';
                        const alert = document.createElement('div');
                        alert.className = `alert ${alertClass} alert-dismissible fade show mt-2`;
                        alert.innerHTML = `
                            <i class="bi bi-check-circle"></i> ${data.message || 'Patient processed successfully'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        patientSearch.parentNode.insertBefore(alert, patientSearch.nextSibling);
                        
                        setTimeout(() => alert.remove(), 3000);
                        
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        form.submit();
                    } else if (data.action === 'not_found') {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        
                        patientSearch.classList.add('is-invalid');
                        let errorDiv = patientSearch.parentNode.querySelector('.invalid-feedback');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            patientSearch.parentNode.insertBefore(errorDiv, patientSearch.nextSibling);
                        }
                        errorDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    patientSearch.classList.add('is-invalid');
                    let errorDiv = patientSearch.parentNode.querySelector('.invalid-feedback');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        patientSearch.parentNode.insertBefore(errorDiv, patientSearch.nextSibling);
                    }
                    errorDiv.textContent = 'Error: ' + error.message;
                });
            });
        }
    }

    // Global function for AJAX search results
    function selectPatient(id, text) {
        const patientSearch = document.getElementById('patient_search');
        const patientIdField = document.getElementById('patient_id');
        
        patientIdField.value = id;
        patientSearch.value = text;
        console.log('✓ Selected via AJAX:', text, 'ID:', id);
    }
});
</script>
@endpush
@endsection
