<script>
const isArabic = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded');
    
    // Get elements
    const quickSelectInput = document.getElementById('quick_select');
    const departmentSelect = document.getElementById('department_id');
    const serviceSelect = document.getElementById('service_id');
    const quickSelectResult = document.getElementById('quick_select_result');
    const priceInfo = document.getElementById('price-info');
    const servicePrice = document.getElementById('service-price');
    
    console.log('Elements found:', {
        quick: !!quickSelectInput,
        dept: !!departmentSelect,
        service: !!serviceSelect
    });
    
    // Quick Select - Handle Enter/Tab
    if (quickSelectInput) {
        quickSelectInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                const value = this.value.trim().toUpperCase();
                console.log('Quick select triggered:', value);
                
                if (!value) return;
                
                // Try to find service by shortcut
                let found = false;
                for (let i = 0; i < departmentSelect.options.length; i++) {
                    const deptOpt = departmentSelect.options[i];
                    if (!deptOpt.value) continue;
                    
                    // Load services for this department
                    const deptId = deptOpt.value;
                    
                    fetch(`/api/departments/${deptId}/services`)
                        .then(r => r.json())
                        .then(services => {
                            if (found) return;
                            
                            for (let s of services) {
                                if (s.shortcut && s.shortcut.toUpperCase() === value) {
                                    // Found match!
                                    found = true;
                                    departmentSelect.value = deptId;
                                    
                                    // Trigger department change
                                    const event = new Event('change');
                                    departmentSelect.dispatchEvent(event);
                                    
                                    setTimeout(() => {
                                        serviceSelect.value = s.id;
                                        
                                        quickSelectResult.innerHTML = '<span class="text-success">✓ ' + (isArabic ? 'تم الاختيار' : 'Selected') + ': ' + 
                                            (isArabic ? s.name_ar : s.name) + '</span>';
                                        this.value = '';
                                        
                                        const patientField = document.getElementById('patient_search');
                                        if (patientField) patientField.focus();
                                    }, 300);
                                    
                                    return;
                                }
                            }
                        });
                }
                
                // If no shortcut found, try numeric format (1, 1.2, etc)
                setTimeout(() => {
                    if (found) return;
                    
                    const parts = value.split('.');
                    const deptIndex = parseInt(parts[0]);
                    const serviceIndex = parts.length > 1 ? parseInt(parts[1]) : 1;
                    
                    for (let i = 0; i < departmentSelect.options.length; i++) {
                        const opt = departmentSelect.options[i];
                        if (opt.dataset.index == deptIndex) {
                            departmentSelect.value = opt.value;
                            const event = new Event('change');
                            departmentSelect.dispatchEvent(event);
                            
                            setTimeout(() => {
                                const validServices = Array.from(serviceSelect.options).filter(o => o.value);
                                if (serviceIndex <= validServices.length) {
                                    serviceSelect.value = validServices[serviceIndex - 1].value;
                                    quickSelectResult.innerHTML = '<span class="text-success">✓ ' + (isArabic ? 'تم الاختيار' : 'Selected') + '</span>';
                                    this.value = '';
                                    const patientField = document.getElementById('patient_search');
                                    if (patientField) patientField.focus();
                                }
                            }, 300);
                            break;
                        }
                    }
                }, 500);
            }
        });
    }
    
    // Department change - load services
    if (departmentSelect) {
        departmentSelect.addEventListener('change', function() {
            const deptId = this.value;
            console.log('Department changed to:', deptId);
            
            if (!deptId) {
                serviceSelect.innerHTML = '<option value="">' + (isArabic ? 'اختر الخدمة' : 'Select Service') + '</option>';
                if (priceInfo) priceInfo.style.display = 'none';
                return;
            }
            
            // Fetch services
            fetch(`/api/departments/${deptId}/services`)
                .then(r => r.json())
                .then(data => {
                    console.log('Services loaded:', data.length);
                    serviceSelect.innerHTML = '<option value="">' + (isArabic ? 'اختر الخدمة' : 'Select Service') + '</option>';
                    
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        const priceText = parseFloat(s.price).toFixed(currencyDecimals);
                        opt.textContent = (isArabic ? s.name_ar : s.name) + ' - ' + priceText + ' ' + currencySymbol;
                        opt.dataset.price = s.price;
                        serviceSelect.appendChild(opt);
                    });
                    
                    if (priceInfo) priceInfo.style.display = 'none';
                })
                .catch(err => console.error('Error loading services:', err));
        });
    }
    
    // Service change - show price
    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (this.value && opt.dataset.price) {
                const priceText = parseFloat(opt.dataset.price).toFixed(currencyDecimals);
                if (servicePrice) servicePrice.textContent = priceText;
                if (priceInfo) priceInfo.style.display = 'block';
            } else if (priceInfo) {
                priceInfo.style.display = 'none';
            }
        });
    }
    
    // Get currency from settings
    const currencySymbol = '{{ \App\Models\Setting::getCurrencySymbol() }}';
    const currencyDecimals = {{ \App\Models\Setting::getCurrencyDecimals() }};
    
    // Patient selection with datalist
    const patientSearch = document.getElementById('patient_search');
    const patientIdField = document.getElementById('patient_id');
    const patientDatalist = document.getElementById('patient_list');
    let selectedPatientId = null;
    
    if (patientSearch && patientDatalist) {
        patientSearch.addEventListener('change', function() {
            const query = this.value.trim().toLowerCase();
            for (let i = 0; i < patientDatalist.options.length; i++) {
                const opt = patientDatalist.options[i];
                if (opt.value.toLowerCase() === query) {
                    selectedPatientId = opt.dataset.id;
                    patientIdField.value = selectedPatientId;
                    console.log('Selected patient:', opt.value, 'ID:', selectedPatientId);
                    return;
                }
            }
            selectedPatientId = null;
            patientIdField.value = '';
        });
        
        patientSearch.addEventListener('blur', function() {
            const query = this.value.trim().toLowerCase();
            for (let i = 0; i < patientDatalist.options.length; i++) {
                const opt = patientDatalist.options[i];
                if (opt.value.toLowerCase() === query) {
                    selectedPatientId = opt.dataset.id;
                    patientIdField.value = selectedPatientId;
                    return;
                }
            }
        });
        
        // Form submit
        const form = patientSearch.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const query = patientSearch.value.trim();
                
                if (!query) {
                    e.preventDefault();
                    patientSearch.classList.add('is-invalid');
                    let err = patientSearch.parentNode.querySelector('.invalid-feedback');
                    if (!err) {
                        err = document.createElement('div');
                        err.className = 'invalid-feedback d-block';
                        err.textContent = isArabic ? 'يرجى اختيار مريض' : 'Please select patient';
                        patientSearch.parentNode.insertBefore(err, patientSearch.nextSibling);
                    }
                    return;
                }
                
                if (selectedPatientId) {
                    patientIdField.value = selectedPatientId;
                    return;
                }
                
                // Check datalist one more time
                for (let i = 0; i < patientDatalist.options.length; i++) {
                    if (patientDatalist.options[i].value.toLowerCase() === query.toLowerCase()) {
                        patientIdField.value = patientDatalist.options[i].dataset.id;
                        return;
                    }
                }
                
                // Create new patient
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '⏳ Creating...';
                
                fetch(`/api/patients/search-or-create?q=${encodeURIComponent(query)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.action === 'created' || data.action === 'found') {
                        patientIdField.value = data.patient.id;
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                        alert.innerHTML = `<i class="bi bi-check-circle"></i> ${data.message || 'OK'}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                        patientSearch.parentNode.insertBefore(alert, patientSearch.nextSibling);
                        setTimeout(() => alert.remove(), 3000);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> ' + (isArabic ? 'إنشاء ودفع' : 'Create & Pay');
                        form.submit();
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> ' + (isArabic ? 'إنشاء ودفع' : 'Create & Pay');
                });
            });
        }
    }
});

function selectPatient(id, text) {
    document.getElementById('patient_id').value = id;
    document.getElementById('patient_search').value = text;
    console.log('Selected:', text, 'ID:', id);
}
</script>
