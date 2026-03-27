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

    // Patient selection with custom dropdown
    const patientSearch = document.getElementById('patient_search');
    const patientIdField = document.getElementById('patient_id');
    const patientResults = document.getElementById('patient_results');
    const patients = @json($patients ?? []);
    let selectedPatientId = null;
    let filteredPatients = [];

    console.log('Patients loaded:', patients.length);

    // Filter patients based on search query
    function filterPatients(query) {
        if (!query || query.length < 1) {
            return patients.slice(0, 10); // Show first 10 when empty
        }
        const lowerQuery = query.toLowerCase();
        return patients.filter(p => {
            const name = p.full_name || '';
            const nationalId = p.national_id || '';
            const phone = p.phone || '';
            return name.toLowerCase().includes(lowerQuery) ||
                nationalId.includes(query) ||
                phone.includes(query);
        }).slice(0, 10);
    }

    // Render patient list
    function renderPatientList(patientList) {
        console.log('Rendering patient list:', patientList.length, 'items');
        if (patientList.length === 0) {
            patientResults.innerHTML = '<div class="text-center py-3 text-muted"><i class="bi bi-inbox"></i> ' + (isArabic ? 'لا توجد نتائج' : 'No results found') + '</div>';
            return;
        }

        let html = '';
        patientList.forEach(p => {
            const name = p.full_name || 'Unknown';
            const info = [];
            if (p.national_id) info.push(`<span class="patient-info-item"><i class="bi bi-card-heading"></i> ${isArabic ? 'هوية' : 'ID'}: ${p.national_id}</span>`);
            if (p.phone) info.push(`<span class="patient-info-item"><i class="bi bi-telephone"></i> ${isArabic ? 'هاتف' : 'Phone'}: ${p.phone}</span>`);
            if (p.birth_date) info.push(`<span class="patient-info-item"><i class="bi bi-calendar-event"></i> ${isArabic ? 'ميلاد' : 'DOB'}: ${p.birth_date}</span>`);

            html += `
                <div class="patient-item" data-id="${p.id}" data-name="${name}">
                    <div class="patient-main">
                        <i class="bi bi-person-circle patient-icon"></i>
                        <div class="patient-name">${name}</div>
                    </div>
                    <div class="patient-details">
                        ${info.join('')}
                    </div>
                </div>
            `;
        });
        patientResults.innerHTML = html;

        // Add click handlers
        patientResults.querySelectorAll('.patient-item').forEach(item => {
            item.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                selectedPatientId = id;
                patientIdField.value = id;
                patientSearch.value = name;
                patientResults.style.display = 'none';
                patientSearch.classList.remove('is-invalid');

                // Visual feedback
                patientResults.querySelectorAll('.patient-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    // Show dropdown
    function showDropdown() {
        const query = patientSearch.value.trim();
        filteredPatients = filterPatients(query);
        renderPatientList(filteredPatients);
        patientResults.style.display = 'block';
        console.log('Dropdown shown, patients:', filteredPatients.length);
    }

    // Patient search input handlers
    if (patientSearch) {
        console.log('Patient search field found');
        console.log('Patient results container:', patientResults);
        
        patientSearch.addEventListener('focus', function() {
            console.log('Patient search focused');
            showDropdown();
        });

        patientSearch.addEventListener('input', function() {
            showDropdown();
        });

        patientSearch.addEventListener('blur', function() {
            // Delay to allow click
            setTimeout(() => {
                patientResults.style.display = 'none';
            }, 200);
        });

        patientSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                patientResults.style.display = 'none';
            }
            if (e.key === 'Enter' && patientResults.style.display === 'block') {
                e.preventDefault();
                const firstItem = patientResults.querySelector('.patient-item');
                if (firstItem) {
                    firstItem.click();
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

                // Check if typed name matches a patient
                const matchedPatient = patients.find(p => (p.full_name || '').toLowerCase() === query.toLowerCase());
                if (matchedPatient) {
                    patientIdField.value = matchedPatient.id;
                    return;
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

<style>
.patient-item {
    display: block;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #f1f3f5;
    cursor: pointer;
    transition: all 0.2s ease;
}

.patient-item:last-child {
    border-bottom: none;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.patient-item:first-child {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}

.patient-item:hover,
.patient-item.active {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 110, 253, 0.1) 100%);
    border-left: 3px solid var(--primary-color);
}

.patient-main {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.patient-icon {
    font-size: 1.25rem;
    color: var(--primary-color);
    flex-shrink: 0;
}

.patient-name {
    font-weight: 600;
    font-size: 1.0rem;
    color: #212529;
}

.patient-details {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 2rem;
}

.patient-info-item {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    font-size: 0.8rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
}

.patient-info-item i {
    font-size: 0.65rem;
}

#patient_results {
    border: 1px solid rgba(13, 110, 253, 0.125) !important;
    z-index: 9999 !important;
    top: 100%;
    left: 0;
    margin-top: 0.25rem;
}

#patient_search:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}
</style>
