@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer')
@section('page-title', app()->getLocale() === 'ar' ? 'مصمم الإيصال' : 'Receipt Designer')

@section('content')
<div class="row g-3">
    <!-- Left Sidebar: Elements -->
    <div class="col-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-ui-checks"></i> {{ app()->getLocale() === 'ar' ? 'العناصر' : 'Elements' }}</h6>
            </div>
            <div class="card-body p-2">
                <p class="text-muted small mb-2">{{ app()->getLocale() === 'ar' ? 'اسحب العناصر إلى منطقة المعاينة' : 'Drag elements to preview area' }}</p>

                <div class="accordion accordion-flush" id="elementsAccordion">
                    <!-- Labels -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#labelsSection">
                                <i class="bi bi-tag me-2"></i> {{ app()->getLocale() === 'ar' ? 'تسميات' : 'Labels' }}
                            </button>
                        </h2>
                        <div id="labelsSection" class="accordion-collapse collapse show" data-bs-parent="#elementsAccordion">
                            <div class="accordion-body p-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_patient" style="cursor: move;">
                                        <i class="bi bi-person text-primary"></i> {{ app()->getLocale() === 'ar' ? 'مريض' : 'Patient' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_service" style="cursor: move;">
                                        <i class="bi bi-heart-pulse text-success"></i> {{ app()->getLocale() === 'ar' ? 'خدمة' : 'Service' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_price" style="cursor: move;">
                                        <i class="bi bi-cash text-warning"></i> {{ app()->getLocale() === 'ar' ? 'سعر' : 'Price' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_date" style="cursor: move;">
                                        <i class="bi bi-calendar text-info"></i> {{ app()->getLocale() === 'ar' ? 'تاريخ' : 'Date' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_queue" style="cursor: move;">
                                        <i class="bi bi-sort-numeric-down text-secondary"></i> {{ app()->getLocale() === 'ar' ? 'طابور' : 'Queue' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="label_cashier" style="cursor: move;">
                                        <i class="bi bi-person-badge text-dark"></i> {{ app()->getLocale() === 'ar' ? 'أمين' : 'Cashier' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Values -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#valuesSection">
                                <i class="bi bi-input-cursor-text me-2"></i> {{ app()->getLocale() === 'ar' ? 'قيم' : 'Values' }}
                            </button>
                        </h2>
                        <div id="valuesSection" class="accordion-collapse collapse" data-bs-parent="#elementsAccordion">
                            <div class="accordion-body p-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_clinic_name" style="cursor: move;">
                                        <i class="bi bi-building text-primary"></i> {{ app()->getLocale() === 'ar' ? 'اسم العيادة' : 'Clinic Name' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_ticket_number" style="cursor: move;">
                                        <i class="bi bi-ticket text-success"></i> {{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_patient_name" style="cursor: move;">
                                        <i class="bi bi-person text-info"></i> {{ app()->getLocale() === 'ar' ? 'اسم المريض' : 'Patient Name' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_service_name" style="cursor: move;">
                                        <i class="bi bi-heart-pulse text-warning"></i> {{ app()->getLocale() === 'ar' ? 'الخدمة' : 'Service' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_price" style="cursor: move;">
                                        <i class="bi bi-cash text-success"></i> {{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_date" style="cursor: move;">
                                        <i class="bi bi-calendar text-primary"></i> {{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_queue_number" style="cursor: move;">
                                        <i class="bi bi-sort-numeric-down text-secondary"></i> {{ app()->getLocale() === 'ar' ? 'رقم الطابور' : 'Queue #' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_cashier_name" style="cursor: move;">
                                        <i class="bi bi-person-badge text-dark"></i> {{ app()->getLocale() === 'ar' ? 'الأمين' : 'Cashier' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_thank_you" style="cursor: move;">
                                        <i class="bi bi-heart text-danger"></i> {{ app()->getLocale() === 'ar' ? 'شكر' : 'Thank You' }}
                                    </div>
                                    <div class="list-group-item list-group-item-action" draggable="true" data-element="value_qr_code" style="cursor: move;">
                                        <i class="bi bi-qr-code text-primary"></i> {{ app()->getLocale() === 'ar' ? 'QR Code' : 'QR Code' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paper Settings -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paperSettings">
                                <i class="bi bi-file-earmark me-2"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات الورقة' : 'Paper Settings' }}
                            </button>
                        </h2>
                        <div id="paperSettings" class="accordion-collapse collapse" data-bs-parent="#elementsAccordion">
                            <div class="accordion-body">
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'العرض (مم)' : 'Width (mm)' }}</label>
                                    <input type="number" class="form-control form-control-sm" id="paper_width" value="80" min="50" max="210">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'الطول (مم)' : 'Height (mm)' }}</label>
                                    <input type="number" class="form-control form-control-sm" id="paper_height" value="200" min="100" max="350">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'حجم الشبكة' : 'Grid Size' }}</label>
                                    <select class="form-select form-select-sm" id="grid_size">
                                        <option value="1">1mm - دقيق</option>
                                        <option value="5" selected>5mm - عادي</option>
                                        <option value="10">10mm - خشن</option>
                                    </select>
                                </div>
                                <button class="btn btn-sm btn-primary w-100" onclick="updatePaperSize()">
                                    <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-lightning"></i> {{ app()->getLocale() === 'ar' ? 'إجراءات سريعة' : 'Quick Actions' }}</h6>
            </div>
            <div class="card-body p-2">
                <div class="d-grid gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="alignTop()">
                        <i class="bi bi-arrow-bar-up"></i> {{ app()->getLocale() === 'ar' ? 'محاذاة للأعلى' : 'Align Top' }}
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="alignCenter()">
                        <i class="bi bi-arrows-move"></i> {{ app()->getLocale() === 'ar' ? 'محاذاة للوسط' : 'Align Center' }}
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="distributeVertical()">
                        <i class="bi bi-grid-3x3-gap"></i> {{ app()->getLocale() === 'ar' ? 'توزيع عمودي' : 'Distribute' }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="previewLayout()">
                        <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة' : 'Preview' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Area: Preview & Properties -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'معاينة الإيصال' : 'Receipt Preview' }}</h6>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-light" onclick="zoomIn()" title="{{ app()->getLocale() === 'ar' ? 'تكبير' : 'Zoom In' }}">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button class="btn btn-light" onclick="zoomOut()" title="{{ app()->getLocale() === 'ar' ? 'تصغير' : 'Zoom Out' }}">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button class="btn btn-light" onclick="resetZoom()" title="{{ app()->getLocale() === 'ar' ? 'إعادة تعيين' : 'Reset' }}">
                        <i class="bi bi-100"></i>
                    </button>
                </div>
            </div>
            <div class="card-body bg-light text-center" style="overflow: auto; min-height: 600px;">
                <div id="receipt_preview" style="border: 2px solid #333; margin: 0 auto; background: white; position: relative; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    <!-- Grid overlay -->
                    <div id="grid_overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; opacity: 0.3;"></div>
                    <!-- Elements will be positioned here -->
                </div>
                <div class="mt-2 text-muted small">
                    <i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'اسحب العناصر أو استخدم الأسهم للتحريك' : 'Drag elements or use arrow keys to move' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Properties -->
    <div class="col-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-sliders"></i> {{ app()->getLocale() === 'ar' ? 'خصائص العنصر' : 'Element Properties' }}</h6>
            </div>
            <div class="card-body">
                <div id="noSelection" class="text-center text-muted py-4">
                    <i class="bi bi-mouse" style="font-size: 2rem;"></i>
                    <p class="mt-2 small">{{ app()->getLocale() === 'ar' ? 'اختر عنصراً لتعديل خصائصه' : 'Select an element to edit properties' }}</p>
                </div>

                <div id="propertiesPanel" style="display: none;">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">X (mm)</label>
                            <input type="number" class="form-control form-control-sm" id="elem_x" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Y (mm)</label>
                            <input type="number" class="form-control form-control-sm" id="elem_y" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'الحجم' : 'Font Size' }}</label>
                            <select class="form-select form-select-sm" id="elem_size">
                                <option value="8">8pt - Extra Small</option>
                                <option value="10">10pt - Small</option>
                                <option value="12" selected>12pt - Medium</option>
                                <option value="14">14pt - Large</option>
                                <option value="16">16pt - Extra Large</option>
                                <option value="18">18pt - XXL</option>
                                <option value="20">20pt - Huge</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'المحاذاة' : 'Alignment' }}</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="elem_align" id="align_left" value="left" autocomplete="off">
                                <label class="btn btn-sm btn-outline-primary" for="align_left"><i class="bi bi-text-left"></i></label>

                                <input type="radio" class="btn-check" name="elem_align" id="align_center" value="center" autocomplete="off">
                                <label class="btn btn-sm btn-outline-primary" for="align_center"><i class="bi bi-text-center"></i></label>

                                <input type="radio" class="btn-check" name="elem_align" id="align_right" value="right" autocomplete="off">
                                <label class="btn btn-sm btn-outline-primary" for="align_right"><i class="bi bi-text-right"></i></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="elem_bold">
                                <label class="form-check-label small fw-bold" for="elem_bold">{{ app()->getLocale() === 'ar' ? 'غامق' : 'Bold' }}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">{{ app()->getLocale() === 'ar' ? 'نص مخصص' : 'Custom Text' }}</label>
                            <input type="text" class="form-control form-control-sm" id="elem_text" placeholder="{{ app()->getLocale() === 'ar' ? 'اختياري' : 'Optional' }}">
                            <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'اتركه فارغاً للنص الافتراضي' : 'Leave empty for default text' }}</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-sm btn-primary" onclick="updateElement()">
                            <i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}
                        </button>
                        <button class="btn btn-sm btn-info" onclick="duplicateElement()">
                            <i class="bi bi-copy"></i> {{ app()->getLocale() === 'ar' ? 'نسخ' : 'Duplicate' }}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteElement()">
                            <i class="bi bi-trash"></i> {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saved Layouts -->
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="bi bi-folder"></i> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}</h6>
            </div>
            <div class="card-body p-2">
                <div class="d-grid gap-2">
                    <button class="btn btn-sm btn-success" onclick="saveLayout()">
                        <i class="bi bi-save"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التصميم' : 'Save Layout' }}
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="resetLayout()">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'إعادة تعيين' : 'Reset to Default' }}
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportLayout()">
                        <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'تصدير' : 'Export' }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="importLayout()">
                        <i class="bi bi-upload"></i> {{ app()->getLocale() === 'ar' ? 'استيراد' : 'Import' }}
                    </button>
                </div>
                <hr>
                <div class="small text-muted">
                    <i class="bi bi-keyboard"></i> {{ app()->getLocale() === 'ar' ? 'اختصارات:' : 'Shortcuts:' }}
                    <ul class="mb-0 mt-1 small">
                        <li><kbd>Ctrl+S</kbd> {{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}</li>
                        <li><kbd>Ctrl+D</kbd> {{ app()->getLocale() === 'ar' ? 'نسخ' : 'Duplicate' }}</li>
                        <li><kbd>Delete</kbd> {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</li>
                        <li><kbd>Arrows</kbd> {{ app()->getLocale() === 'ar' ? 'تحريك' : 'Move' }}</li>
                        <li><kbd>Shift+Arrows</kbd> {{ app()->getLocale() === 'ar' ? 'تحريك سريع' : 'Fast Move' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ app()->getLocale() === 'ar' ? 'استيراد تصميم' : 'Import Layout' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="importJson" rows="10" placeholder="Paste JSON here..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                <button type="button" class="btn btn-primary" onclick="doImport()">{{ app()->getLocale() === 'ar' ? 'استيراد' : 'Import' }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
#receipt_preview {
    width: 300px;
    height: 750px;
    transition: width 0.3s, height 0.3s;
    position: relative;
}
.receipt-element {
    position: absolute;
    cursor: move;
    padding: 3px 5px;
    border: 2px dashed transparent;
    white-space: nowrap;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 3px;
    user-select: none;
    transition: border-color 0.2s, background-color 0.2s;
}
.receipt-element:hover {
    border: 2px dashed #0d6efd;
    background: rgba(13, 110, 253, 0.15);
}
.receipt-element.selected {
    border: 2px solid #0d6efd;
    background: rgba(13, 110, 253, 0.25);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}
.receipt-element:active {
    cursor: grabbing;
}
.list-group-item {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
}
.accordion-button {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
#grid_overlay {
    background-image: 
        linear-gradient(rgba(13, 110, 253, 0.15) 1px, transparent 1px),
        linear-gradient(90deg, rgba(13, 110, 253, 0.15) 1px, transparent 1px);
    background-size: 19px 19px;
    pointer-events: none;
    z-index: 1;
}
/* Prevent text selection during drag */
.receipt-element * {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
}
</style>
@endpush

@push('scripts')
<script>
let currentLayout = {};
let selectedElement = null;
let paperWidth = 80;
let paperHeight = 200;
let elementCounter = 0;
let zoomLevel = 1;
let gridSize = 5;

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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadLayout();
    updatePaperSize();
    updateGrid();

    // Drag handlers for element palette
    document.querySelectorAll('[data-element]').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('element', this.dataset.element);
            e.dataTransfer.effectAllowed = 'copy';
        });
    });

    const preview = document.getElementById('receipt_preview');
    
    preview.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    });

    preview.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const elementType = e.dataTransfer.getData('element');

        if (elementType && elementDefinitions[elementType]) {
            elementCounter++;
            const newKey = 'elem_' + elementCounter;

            const rect = preview.getBoundingClientRect();
            const x = Math.round((e.clientX - rect.left) / zoomLevel / 3.78);
            const y = Math.round((e.clientY - rect.top) / zoomLevel / 3.78);

            currentLayout[newKey] = {
                type: elementType,
                x: Math.max(0, x),
                y: Math.max(0, y),
                size: 12,
                align: 'left',
                bold: false,
                text: elementDefinitions[elementType].text
            };

            renderElements();
            setTimeout(() => selectElement(newKey), 50);
        }
    });

    // Grid size change
    document.getElementById('grid_size').addEventListener('change', function() {
        gridSize = parseInt(this.value);
        updateGrid();
    });
    
    // Click on preview background to deselect
    preview.addEventListener('click', function(e) {
        if (e.target === this || e.target.id === 'grid_overlay') {
            selectElement(null);
        }
    });
});

function updateGrid() {
    const gridOverlay = document.getElementById('grid_overlay');
    const pixelSize = gridSize * 3.78 * zoomLevel;
    gridOverlay.style.backgroundSize = `${pixelSize}px ${pixelSize}px`;
}

// Load layout
function loadLayout() {
    const savedWidth = localStorage.getItem('receipt_paper_width');
    const savedHeight = localStorage.getItem('receipt_paper_height');

    if (savedWidth) {
        paperWidth = parseInt(savedWidth);
        document.getElementById('paper_width').value = paperWidth;
    }

    if (savedHeight) {
        paperHeight = parseInt(savedHeight);
        document.getElementById('paper_height').value = paperHeight;
    }

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
    
    // Preserve grid overlay
    const gridOverlay = document.getElementById('grid_overlay');
    preview.innerHTML = '';
    if (gridOverlay) {
        preview.appendChild(gridOverlay);
    } else {
        const newOverlay = document.createElement('div');
        newOverlay.id = 'grid_overlay';
        newOverlay.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; opacity: 0.3;';
        preview.appendChild(newOverlay);
    }

    // Sort elements by Y position for proper rendering order
    const sortedElements = Object.entries(currentLayout).sort((a, b) => a[1].y - b[1].y);

    for (const [key, config] of sortedElements) {
        const elem = document.createElement('div');
        elem.className = 'receipt-element';
        elem.dataset.key = key;
        elem.style.left = (config.x * 3.78) + 'px';
        elem.style.top = (config.y * 3.78) + 'px';
        elem.style.fontSize = config.size + 'pt';
        elem.style.textAlign = config.align;
        elem.style.fontWeight = config.bold ? 'bold' : 'normal';
        elem.style.zIndex = '10';
        elem.style.cursor = 'move';
        elem.style.userSelect = 'none';
        
        // Display text based on type with placeholder data
        let displayText = config.text || elementDefinitions[config.type]?.text || config.type;
        displayText = displayText.replace('{clinic_name}', '{{ \App\Models\Setting::getClinicName() }}');
        displayText = displayText.replace('{ticket_number}', 'TKT-001');
        displayText = displayText.replace('{patient_name}', 'أحمد محمد');
        displayText = displayText.replace('{service_name}', 'كشفية عامة');
        displayText = displayText.replace('{price}', '25.00 JOD');
        displayText = displayText.replace('{created_at}', '2024-03-27 10:30');
        displayText = displayText.replace('{queue_number}', '15');
        displayText = displayText.replace('{cashier_name}', 'موظف 1');
        
        if (config.type === 'value_qr_code') {
            elem.innerHTML = '<div style="width: 40px; height: 40px; background: #000; margin: 0 auto;"></div>';
        } else {
            elem.textContent = displayText;
        }

        // Use mousedown for better control
        elem.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            selectElement(key);
        });

        preview.appendChild(elem);
    }
    
    // Re-apply drag functionality to all elements
    document.querySelectorAll('.receipt-element').forEach(makeDraggable);
    
    // Re-select if there was a selected element
    if (selectedElement) {
        selectElement(selectedElement);
    }
}

function mmToPx(mm) {
    return mm * 3.78;
}

function pxToMm(px) {
    return px / 3.78;
}

// Make element draggable
function makeDraggable(elem) {
    let isDragging = false;
    let startX, startY, initialLeft, initialTop;
    let dragOffsetX = 0, dragOffsetY = 0;

    elem.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return; // Only left click
        
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        initialLeft = elem.offsetLeft;
        initialTop = elem.offsetTop;
        
        // Calculate offset within element
        const rect = elem.getBoundingClientRect();
        dragOffsetX = e.clientX - rect.left;
        dragOffsetY = e.clientY - rect.top;
        
        elem.style.cursor = 'grabbing';
        elem.style.zIndex = '1000'; // Bring to front while dragging
        
        selectElement(elem.dataset.key);
        
        e.preventDefault();
        e.stopPropagation();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        const preview = document.getElementById('receipt_preview');
        const previewRect = preview.getBoundingClientRect();
        
        // Calculate new position relative to preview
        let newLeft = (e.clientX - previewRect.left - dragOffsetX / zoomLevel) * zoomLevel;
        let newTop = (e.clientY - previewRect.top - dragOffsetY / zoomLevel) * zoomLevel;
        
        // Snap to grid
        const gridSizePx = gridSize * 3.78 * zoomLevel;
        newLeft = Math.round(newLeft / gridSizePx) * gridSizePx;
        newTop = Math.round(newTop / gridSizePx) * gridSizePx;
        
        // Boundaries
        const maxLeft = (paperWidth * 3.78 * zoomLevel) - elem.offsetWidth;
        const maxTop = (paperHeight * 3.78 * zoomLevel) - elem.offsetHeight;
        
        newLeft = Math.max(0, Math.min(newLeft, maxLeft));
        newTop = Math.max(0, Math.min(newTop, maxTop));
        
        elem.style.left = newLeft + 'px';
        elem.style.top = newTop + 'px';
        
        // Update properties panel in real-time
        if (selectedElement === elem.dataset.key) {
            document.getElementById('elem_x').value = Math.round(newLeft / 3.78 / zoomLevel);
            document.getElementById('elem_y').value = Math.round(newTop / 3.78 / zoomLevel);
        }
    });

    document.addEventListener('mouseup', function(e) {
        if (!isDragging) return;
        isDragging = false;
        elem.style.cursor = 'move';
        elem.style.zIndex = '100'; // Return to selected z-index
        
        // Update layout data
        if (selectedElement === elem.dataset.key) {
            currentLayout[selectedElement].x = Math.round(parseFloat(elem.style.left) / 3.78 / zoomLevel);
            currentLayout[selectedElement].y = Math.round(parseFloat(elem.style.top) / 3.78 / zoomLevel);
        }
    });
}

// Select element
function selectElement(key) {
    if (!key || !currentLayout[key]) {
        // Deselect all
        selectedElement = null;
        document.querySelectorAll('.receipt-element').forEach(el => el.classList.remove('selected'));
        document.getElementById('noSelection').style.display = 'block';
        document.getElementById('propertiesPanel').style.display = 'none';
        return;
    }

    selectedElement = key;

    // Remove selected class from all elements
    document.querySelectorAll('.receipt-element').forEach(el => {
        el.classList.remove('selected');
        el.style.zIndex = '10';
    });

    // Find and select the specific element
    const elem = document.querySelector(`[data-key="${key}"]`);
    if (elem) {
        elem.classList.add('selected');
        elem.style.zIndex = '100'; // Bring to front

        // Update properties panel
        document.getElementById('elem_x').value = currentLayout[key].x;
        document.getElementById('elem_y').value = currentLayout[key].y;
        document.getElementById('elem_size').value = currentLayout[key].size;
        document.getElementById('elem_align').value = currentLayout[key].align;
        document.getElementById('elem_bold').checked = currentLayout[key].bold;
        document.getElementById('elem_text').value = currentLayout[key].text || '';

        // Update radio buttons for alignment
        const alignRadio = document.querySelector(`input[name="elem_align"][value="${currentLayout[key].align}"]`);
        if (alignRadio) alignRadio.checked = true;

        document.getElementById('noSelection').style.display = 'none';
        document.getElementById('propertiesPanel').style.display = 'block';
    }
}

// Update element
function updateElement() {
    if (!selectedElement) return;

    currentLayout[selectedElement].x = parseInt(document.getElementById('elem_x').value);
    currentLayout[selectedElement].y = parseInt(document.getElementById('elem_y').value);
    currentLayout[selectedElement].size = parseInt(document.getElementById('elem_size').value);
    currentLayout[selectedElement].align = document.querySelector('input[name="elem_align"]:checked').value;
    currentLayout[selectedElement].bold = document.getElementById('elem_bold').checked;
    
    const customText = document.getElementById('elem_text').value;
    if (customText) {
        currentLayout[selectedElement].text = customText;
    }

    renderElements();
    selectElement(selectedElement);
}

// Duplicate element
function duplicateElement() {
    if (!selectedElement) {
        showNotification('{{ app()->getLocale() === 'ar' ? 'اختر عنصراً أولاً' : 'Select an element first' }}', 'warning');
        return;
    }

    elementCounter++;
    const newKey = 'elem_' + elementCounter;

    currentLayout[newKey] = JSON.parse(JSON.stringify(currentLayout[selectedElement]));
    currentLayout[newKey].x += 5;
    currentLayout[newKey].y += 5;

    renderElements();
    selectElement(newKey);
}

// Delete element
function deleteElement() {
    if (!selectedElement) return;

    delete currentLayout[selectedElement];
    selectedElement = null;
    renderElements();

    document.getElementById('noSelection').style.display = 'block';
    document.getElementById('propertiesPanel').style.display = 'none';
}

// Zoom functions
function zoomIn() {
    zoomLevel = Math.min(zoomLevel + 0.1, 2);
    applyZoom();
}

function zoomOut() {
    zoomLevel = Math.max(zoomLevel - 0.1, 0.5);
    applyZoom();
}

function resetZoom() {
    zoomLevel = 1;
    applyZoom();
}

function applyZoom() {
    const preview = document.getElementById('receipt_preview');
    preview.style.width = (mmToPx(paperWidth) * zoomLevel) + 'px';
    preview.style.height = (mmToPx(paperHeight) * zoomLevel) + 'px';
    updateGrid();
    renderElements();
}

// Update paper size
function updatePaperSize() {
    paperWidth = parseInt(document.getElementById('paper_width').value);
    paperHeight = parseInt(document.getElementById('paper_height').value);

    localStorage.setItem('receipt_paper_width', paperWidth);
    localStorage.setItem('receipt_paper_height', paperHeight);

    const preview = document.getElementById('receipt_preview');
    preview.style.width = (mmToPx(paperWidth) * zoomLevel) + 'px';
    preview.style.height = (mmToPx(paperHeight) * zoomLevel) + 'px';
    
    // Re-render to update boundaries
    renderElements();
}

// Quick actions
function alignTop() {
    if (!selectedElement) return;
    currentLayout[selectedElement].y = 0;
    renderElements();
    selectElement(selectedElement);
}

function alignCenter() {
    if (!selectedElement) return;
    currentLayout[selectedElement].x = Math.round(paperWidth / 2);
    renderElements();
    selectElement(selectedElement);
}

function distributeVertical() {
    const elements = Object.keys(currentLayout);
    if (elements.length < 2) return;

    const sorted = elements.sort((a, b) => currentLayout[a].y - currentLayout[b].y);
    const startY = currentLayout[sorted[0]].y;
    const endY = currentLayout[sorted[sorted.length - 1]].y;
    const spacing = (endY - startY) / (sorted.length - 1);

    sorted.forEach((key, index) => {
        currentLayout[key].y = Math.round(startY + (index * spacing));
    });

    renderElements();
}

// Preview layout
function previewLayout() {
    const preview = document.getElementById('receipt_preview');
    preview.scrollIntoView({ behavior: 'smooth' });
}

// Save layout
function saveLayout() {
    localStorage.setItem('receipt_layout', JSON.stringify(currentLayout));
    localStorage.setItem('receipt_paper_width', paperWidth);
    localStorage.setItem('receipt_paper_height', paperHeight);

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('CSRF token not found', 'danger');
        return;
    }

    const url = '{{ route('settings.printing.save-layout') }}';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            layout: currentLayout,
            paper_width: paperWidth,
            paper_height: paperHeight
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('{{ app()->getLocale() === 'ar' ? 'تم الحفظ بنجاح!' : 'Saved successfully!' }}', 'success');
        } else {
            showNotification('{{ app()->getLocale() === 'ar' ? 'فشل الحفظ' : 'Save failed' }}', 'danger');
        }
    })
    .catch(error => {
        showNotification(error.message || '{{ app()->getLocale() === 'ar' ? 'حدث خطأ' : 'An error occurred' }}', 'danger');
    });
}

// Reset layout
function resetLayout() {
    if (confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟ سيتم فقدان جميع التغييرات' : 'Are you sure? All changes will be lost' }}')) {
        currentLayout = JSON.parse(JSON.stringify(defaultLayout));
        renderElements();
        localStorage.removeItem('receipt_layout');
        selectedElement = null;
        document.getElementById('noSelection').style.display = 'block';
        document.getElementById('propertiesPanel').style.display = 'none';
    }
}

// Export layout
function exportLayout() {
    const json = JSON.stringify(currentLayout, null, 2);
    navigator.clipboard.writeText(json).then(() => {
        showNotification('{{ app()->getLocale() === 'ar' ? 'تم نسخ التصميم للحافظة' : 'Layout copied to clipboard' }}', 'success');
    });
}

// Import layout
function importLayout() {
    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();
}

function doImport() {
    try {
        const json = document.getElementById('importJson').value;
        const imported = JSON.parse(json);
        currentLayout = imported;
        renderElements();
        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
        showNotification('{{ app()->getLocale() === 'ar' ? 'تم الاستيراد بنجاح' : 'Imported successfully' }}', 'success');
    } catch (e) {
        showNotification('{{ app()->getLocale() === 'ar' ? 'JSON غير صالح' : 'Invalid JSON' }}', 'danger');
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Delete' || e.key === 'Backspace') {
        if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            deleteElement();
        }
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveLayout();
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
        e.preventDefault();
        duplicateElement();
    }

    if (selectedElement && ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
        e.preventDefault();
        const step = e.shiftKey ? 5 : 1;

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

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-circle' : 'info-circle')}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>
@endpush
