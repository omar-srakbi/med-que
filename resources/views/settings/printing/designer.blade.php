@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer')
@section('page-title', app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Elements Panel -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ui-checks"></i> {{ app()->getLocale() === 'ar' ? 'العناصر' : 'Elements' }}
            </div>
            <div class="card-body">
                <p class="text-muted small">{{ app()->getLocale() === 'ar' ? 'اسحب العناصر إلى منطقة المعاينة' : 'Drag elements to preview area' }}</p>
                
                <h6 class="small text-muted">{{ app()->getLocale() === 'ar' ? 'تسميات' : 'Labels' }}</h6>
                <div class="list-group mb-3">
                    <div class="list-group-item" draggable="true" data-element="label_patient">
                        <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'مريض' : 'Patient' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="label_service">
                        <i class="bi bi-heart-pulse"></i> {{ app()->getLocale() === 'ar' ? 'خدمة' : 'Service' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="label_price">
                        <i class="bi bi-cash"></i> {{ app()->getLocale() === 'ar' ? 'سعر' : 'Price' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="label_date">
                        <i class="bi bi-calendar"></i> {{ app()->getLocale() === 'ar' ? 'تاريخ' : 'Date' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="label_queue">
                        <i class="bi bi-sort-numeric-down"></i> {{ app()->getLocale() === 'ar' ? 'طابور' : 'Queue' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="label_cashier">
                        <i class="bi bi-person-badge"></i> {{ app()->getLocale() === 'ar' ? 'أمين' : 'Cashier' }}
                    </div>
                </div>
                
                <h6 class="small text-muted">{{ app()->getLocale() === 'ar' ? 'قيم' : 'Values' }}</h6>
                <div class="list-group mb-3">
                    <div class="list-group-item" draggable="true" data-element="value_clinic_name">
                        <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'اسم العيادة' : 'Clinic Name' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_ticket_number">
                        <i class="bi bi-ticket"></i> {{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket Number' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_patient_name">
                        <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'اسم المريض' : 'Patient Name' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_service_name">
                        <i class="bi bi-heart-pulse"></i> {{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_price">
                        <i class="bi bi-cash"></i> {{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_date">
                        <i class="bi bi-calendar"></i> {{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_queue_number">
                        <i class="bi bi-sort-numeric-down"></i> {{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue Number' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_cashier_name">
                        <i class="bi bi-person-badge"></i> {{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_thank_you">
                        <i class="bi bi-heart"></i> {{ app()->getLocale() === 'ar' ? 'شكر' : 'Thank You' }}
                    </div>
                    <div class="list-group-item" draggable="true" data-element="value_qr_code">
                        <i class="bi bi-qr-code"></i> {{ app()->getLocale() === 'ar' ? 'QR Code' : 'QR Code' }}
                    </div>
                </div>
                
                <hr>
                
                <h6>{{ app()->getLocale() === 'ar' ? 'إعدادات الورقة' : 'Paper Settings' }}</h6>
                <div class="mb-2">
                    <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'العرض (مم)' : 'Width (mm)' }}</label>
                    <input type="number" class="form-control form-control-sm" id="paper_width" value="80" min="50" max="210">
                </div>
                <div class="mb-2">
                    <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'الطول (مم)' : 'Height (mm)' }}</label>
                    <input type="number" class="form-control form-control-sm" id="paper_height" value="200" min="100" max="350">
                </div>
                <button class="btn btn-sm btn-primary w-100 mb-2" onclick="updatePaperSize()">
                    {{ app()->getLocale() === 'ar' ? 'تحديث الحجم' : 'Update Size' }}
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Preview Area -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة' : 'Preview' }}</span>
                <div>
                    <button class="btn btn-sm btn-success" onclick="saveLayout()">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="resetLayout()">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'إعادة تعيين' : 'Reset' }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="receipt_preview" style="border: 2px solid #000; margin: 0 auto; background: white; position: relative; overflow: hidden;">
                    <!-- Elements will be positioned here -->
                </div>
                
                <div class="mt-3">
                    <h6>{{ app()->getLocale() === 'ar' ? 'خصائص العنصر' : 'Element Properties' }}</h6>
                    <div class="row g-2">
                        <div class="col-3">
                            <label class="form-label small">X (mm)</label>
                            <input type="number" class="form-control form-control-sm" id="elem_x" value="0">
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Y (mm)</label>
                            <input type="number" class="form-control form-control-sm" id="elem_y" value="0">
                        </div>
                        <div class="col-2">
                            <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'الحجم' : 'Size' }}</label>
                            <select class="form-select form-select-sm" id="elem_size">
                                <option value="8">XS</option>
                                <option value="10">Small</option>
                                <option value="12" selected>Medium</option>
                                <option value="14">Large</option>
                                <option value="16">XL</option>
                                <option value="18">XXL</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'المحاذاة' : 'Align' }}</label>
                            <select class="form-select form-select-sm" id="elem_align">
                                <option value="left">{{ app()->getLocale() === 'ar' ? 'يسار' : 'Left' }}</option>
                                <option value="center">{{ app()->getLocale() === 'ar' ? 'وسط' : 'Center' }}</option>
                                <option value="right">{{ app()->getLocale() === 'ar' ? 'يمين' : 'Right' }}</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'غامق' : 'Bold' }}</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="elem_bold">
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">{{ app()->getLocale() === 'ar' ? 'نص مخصص' : 'Custom Text' }}</label>
                            <input type="text" class="form-control form-control-sm" id="elem_text" placeholder="Optional">
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-4">
                            <button class="btn btn-sm btn-primary w-100" onclick="updateElement()">
                                <i class="bi bi-check"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-sm btn-info w-100" onclick="duplicateElement()">
                                <i class="bi bi-copy"></i> {{ app()->getLocale() === 'ar' ? 'نسخ' : 'Duplicate' }}
                            </button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-sm btn-danger w-100" onclick="deleteElement()">
                                <i class="bi bi-trash"></i> {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#receipt_preview {
    width: 300px;
    height: 750px;
}
.receipt-element {
    position: absolute;
    cursor: move;
    padding: 2px;
    border: 1px dashed transparent;
    white-space: nowrap;
}
.receipt-element:hover {
    border: 1px dashed #007bff;
    background: rgba(0, 123, 255, 0.1);
}
.receipt-element.selected {
    border: 2px solid #007bff;
    background: rgba(0, 123, 255, 0.2);
}
</style>

@push('scripts')
<script>
let currentLayout = {};
let selectedElement = null;
let paperWidth = 80;
let paperHeight = 200;
let elementCounter = 0;

// Element definitions
const elementDefinitions = {
    label_patient: { text: '{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:', default: true },
    label_service: { text: '{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}:', default: true },
    label_price: { text: '{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}:', default: true },
    label_date: { text: '{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}:', default: true },
    label_queue: { text: '{{ app()->getLocale() === 'ar' ? 'الطابور' : 'Queue' }}:', default: true },
    label_cashier: { text: '{{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}:', default: true },
    
    value_clinic_name: { text: '{clinic_name}', default: true },
    value_ticket_number: { text: '{ticket_number}', default: true },
    value_patient_name: { text: '{patient_name}', default: true },
    value_service_name: { text: '{service_name}', default: true },
    value_price: { text: '{price}', default: true },
    value_date: { text: '{created_at}', default: true },
    value_queue_number: { text: '{queue_number}', default: true },
    value_cashier_name: { text: '{cashier_name}', default: true },
    value_thank_you: { text: '{{ app()->getLocale() === 'ar' ? 'شكراً لزيارتكم' : 'Thank you' }}', default: true },
    value_qr_code: { text: '[QR]', default: true }
};

// Default layout
const defaultLayout = {
    elem_1: { type: 'value_clinic_name', x: 0, y: 5, size: 14, align: 'center', bold: true, text: '{clinic_name}' },
    elem_2: { type: 'value_ticket_number', x: 0, y: 15, size: 12, align: 'center', bold: true, text: '{ticket_number}' },
    elem_3: { type: 'label_patient', x: 0, y: 25, size: 12, align: 'left', bold: false, text: '{{ app()->getLocale() === 'ar' ? 'المريض' : 'Patient' }}:' },
    elem_4: { type: 'value_patient_name', x: 40, y: 25, size: 12, align: 'left', bold: false, text: '{patient_name}' },
    elem_5: { type: 'label_service', x: 0, y: 35, size: 12, align: 'left', bold: false, text: '{{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}:' },
    elem_6: { type: 'value_service_name', x: 40, y: 35, size: 12, align: 'left', bold: false, text: '{service_name}' },
    elem_7: { type: 'label_price', x: 0, y: 45, size: 12, align: 'left', bold: false, text: '{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}:' },
    elem_8: { type: 'value_price', x: 40, y: 45, size: 12, align: 'right', bold: true, text: '{price}' },
    elem_9: { type: 'value_date', x: 0, y: 55, size: 10, align: 'left', bold: false, text: '{created_at}' },
    elem_10: { type: 'value_thank_you', x: 0, y: 85, size: 12, align: 'center', bold: false, text: '{{ app()->getLocale() === 'ar' ? 'شكراً لزيارتكم' : 'Thank you' }}' }
};

// Load layout
function loadLayout() {
    // Load paper size from localStorage or use defaults
    const savedWidth = localStorage.getItem('paper_width');
    const savedHeight = localStorage.getItem('paper_height');
    
    if (savedWidth) {
        paperWidth = parseInt(savedWidth);
        document.getElementById('paper_width').value = paperWidth;
    }
    
    if (savedHeight) {
        paperHeight = parseInt(savedHeight);
        document.getElementById('paper_height').value = paperHeight;
    }
    
    // Load layout
    const saved = localStorage.getItem('receipt_layout');
    if (saved) {
        currentLayout = JSON.parse(saved);
    } else {
        currentLayout = JSON.parse(JSON.stringify(defaultLayout));
    }
    renderElements();
}

// Render all elements
function renderElements() {
    const preview = document.getElementById('receipt_preview');
    preview.innerHTML = '';
    
    for (const [key, config] of Object.entries(currentLayout)) {
        const elem = document.createElement('div');
        elem.className = 'receipt-element';
        elem.dataset.key = key;
        elem.style.left = mmToPx(config.x) + 'px';
        elem.style.top = mmToPx(config.y) + 'px';
        elem.style.fontSize = config.size + 'pt';
        elem.style.textAlign = config.align;
        elem.style.fontWeight = config.bold ? 'bold' : 'normal';
        elem.textContent = config.text;
        
        elem.addEventListener('click', function(e) {
            e.stopPropagation();
            selectElement(key);
        });
        
        makeDraggable(elem);
        preview.appendChild(elem);
    }
}

// Convert mm to pixels
function mmToPx(mm) {
    return mm * 3.78;
}

function pxToMm(px) {
    return px / 3.78;
}

// Make element draggable
function makeDraggable(elem) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    
    elem.onmousedown = dragMouseDown;
    
    function dragMouseDown(e) {
        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }
    
    function elementDrag(e) {
        e.preventDefault();
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        elem.style.top = (elem.offsetTop - pos2) + 'px';
        elem.style.left = (elem.offsetLeft - pos1) + 'px';
        
        if (selectedElement === elem.dataset.key) {
            document.getElementById('elem_x').value = Math.round(pxToMm(elem.offsetLeft));
            document.getElementById('elem_y').value = Math.round(pxToMm(elem.offsetTop));
        }
    }
    
    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
        
        if (selectedElement) {
            currentLayout[selectedElement].x = Math.round(pxToMm(elem.offsetLeft));
            currentLayout[selectedElement].y = Math.round(pxToMm(elem.offsetTop));
        }
    }
}

// Select element
function selectElement(key) {
    selectedElement = key;
    
    document.querySelectorAll('.receipt-element').forEach(el => el.classList.remove('selected'));
    
    const elem = document.querySelector(`[data-key="${key}"]`);
    if (elem) {
        elem.classList.add('selected');
        
        document.getElementById('elem_x').value = currentLayout[key].x;
        document.getElementById('elem_y').value = currentLayout[key].y;
        document.getElementById('elem_size').value = currentLayout[key].size;
        document.getElementById('elem_align').value = currentLayout[key].align;
        document.getElementById('elem_bold').checked = currentLayout[key].bold;
        document.getElementById('elem_text').value = currentLayout[key].text || '';
    }
}

// Update element
function updateElement() {
    if (!selectedElement) return;
    
    currentLayout[selectedElement].x = parseInt(document.getElementById('elem_x').value);
    currentLayout[selectedElement].y = parseInt(document.getElementById('elem_y').value);
    currentLayout[selectedElement].size = parseInt(document.getElementById('elem_size').value);
    currentLayout[selectedElement].align = document.getElementById('elem_align').value;
    currentLayout[selectedElement].bold = document.getElementById('elem_bold').checked;
    currentLayout[selectedElement].text = document.getElementById('elem_text').value;
    
    renderElements();
    selectElement(selectedElement);
}

// Duplicate element
function duplicateElement() {
    if (!selectedElement) {
        alert('{{ app()->getLocale() === 'ar' ? 'اختر عنصراً أولاً' : 'Select an element first' }}');
        return;
    }
    
    elementCounter++;
    const newKey = 'elem_' + elementCounter;
    
    // Copy current element with offset
    currentLayout[newKey] = JSON.parse(JSON.stringify(currentLayout[selectedElement]));
    currentLayout[newKey].x += 5; // Offset by 5mm
    currentLayout[newKey].y += 5;
    
    renderElements();
    selectElement(newKey);
}

// Delete element
function deleteElement() {
    if (!selectedElement) {
        return; // Silently return if nothing selected
    }
    
    // Delete without confirmation
    delete currentLayout[selectedElement];
    selectedElement = null;
    renderElements();
    
    // Clear properties
    document.getElementById('elem_x').value = '';
    document.getElementById('elem_y').value = '';
    document.getElementById('elem_text').value = '';
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Delete key - delete selected element
    if (e.key === 'Delete' || e.key === 'Backspace') {
        // Only delete if not typing in an input field
        if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            deleteElement();
        }
    }
    
    // Ctrl/Cmd + S - Save layout
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveLayout();
    }
    
    // Ctrl/Cmd + D - Duplicate element
    if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        duplicateElement();
    }
    
    // Arrow keys - Move selected element
    if (selectedElement && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
        e.preventDefault();
        const step = e.shiftKey ? 5 : 1; // Hold Shift for larger steps
        
        if (e.key === 'ArrowUp') {
            currentLayout[selectedElement].y -= step;
        } else if (e.key === 'ArrowDown') {
            currentLayout[selectedElement].y += step;
        } else if (e.key === 'ArrowLeft') {
            currentLayout[selectedElement].x -= step;
        } else if (e.key === 'ArrowRight') {
            currentLayout[selectedElement].x += step;
        }
        
        renderElements();
        selectElement(selectedElement);
    }
});

// Update paper size
function updatePaperSize() {
    paperWidth = parseInt(document.getElementById('paper_width').value);
    paperHeight = parseInt(document.getElementById('paper_height').value);
    
    // Save to localStorage immediately
    localStorage.setItem('paper_width', paperWidth);
    localStorage.setItem('paper_height', paperHeight);
    
    const preview = document.getElementById('receipt_preview');
    preview.style.width = mmToPx(paperWidth) + 'px';
    preview.style.height = mmToPx(paperHeight) + 'px';
    
    console.log('Paper size updated:', paperWidth, 'x', paperHeight);
}

// Save layout
function saveLayout() {
    console.log('Saving layout...');
    console.log('Layout:', currentLayout);
    console.log('Paper size:', paperWidth, 'x', paperHeight);
    
    // Save to browser
    localStorage.setItem('receipt_layout', JSON.stringify(currentLayout));
    localStorage.setItem('paper_width', paperWidth);
    localStorage.setItem('paper_height', paperHeight);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showError('CSRF token not found');
        return;
    }
    
    const url = '{{ route('settings.printing.save-layout') }}';
    console.log('Saving to URL:', url);
    
    // Prepare data to send
    const dataToSend = {
        layout: currentLayout,
        paper_width: paperWidth,
        paper_height: paperHeight
    };
    console.log('Sending data:', dataToSend);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(dataToSend)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server response:', text);
                throw new Error('Server error: ' + response.status + ' - ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Save response:', data);
        if (data.success) {
            showSuccess('{{ app()->getLocale() === 'ar' ? 'تم الحفظ بنجاح!' : 'Saved successfully!' }}');
        } else {
            showError('{{ app()->getLocale() === 'ar' ? 'فشل الحفظ' : 'Save failed' }}');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        showError(error.message || '{{ app()->getLocale() === 'ar' ? 'حدث خطأ' : 'An error occurred' }}');
    });
}

// Show success notification
function showSuccess(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    notification.innerHTML = `
        <i class="bi bi-check-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Show error notification
function showError(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    notification.innerHTML = `
        <i class="bi bi-exclamation-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

// Reset layout
function resetLayout() {
    if (confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟ سيتم فقدان جميع التغييرات' : 'Are you sure? All changes will be lost' }}')) {
        currentLayout = JSON.parse(JSON.stringify(defaultLayout));
        renderElements();
        localStorage.removeItem('receipt_layout');
        selectedElement = null;
    }
}

// Drag and drop from panel
document.addEventListener('DOMContentLoaded', function() {
    loadLayout();
    updatePaperSize();
    
    // Add drag handlers
    document.querySelectorAll('[data-element]').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('element', this.dataset.element);
        });
    });
    
    const preview = document.getElementById('receipt_preview');
    preview.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    preview.addEventListener('drop', function(e) {
        e.preventDefault();
        const elementType = e.dataTransfer.getData('element');
        
        if (elementType && elementDefinitions[elementType]) {
            elementCounter++;
            const newKey = 'elem_' + elementCounter;
            
            const rect = preview.getBoundingClientRect();
            const x = Math.round(pxToMm(e.clientX - rect.left));
            const y = Math.round(pxToMm(e.clientY - rect.top));
            
            currentLayout[newKey] = {
                type: elementType,
                x: x,
                y: y,
                size: 12,
                align: 'left',
                bold: false,
                text: elementDefinitions[elementType].text
            };
            
            renderElements();
        }
    });
});
</script>
@endpush
@endsection
