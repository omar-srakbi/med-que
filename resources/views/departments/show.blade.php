@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات القسم' : 'Department Details')
@section('page-title', app()->getLocale() === 'ar' ? 'بيانات القسم' : 'Department Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-building"></i> {{ app()->getLocale() === 'ar' ? 'معلومات القسم' : 'Department Information' }}
            </div>
            <div class="card-body">
                <h4>{{ app()->getLocale() === 'ar' ? $department->name_ar : $department->name }}</h4>
                <p class="text-muted">{{ $department->description ?? '-' }}</p>
                <hr>
                <p>
                    <strong>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}:</strong><br>
                    @if($department->is_active)
                        <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                    @else
                        <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                    @endif
                </p>
                <p>
                    <strong>{{ app()->getLocale() === 'ar' ? 'عدد الخدمات' : 'Services Count' }}:</strong><br>
                    <span class="badge bg-primary">{{ $department->services->count() }}</span>
                </p>
                
                <hr>
                <h6 class="mb-3"><i class="bi bi-ticket-perforated"></i> {{ app()->getLocale() === 'ar' ? 'إعدادات التذاكر' : 'Ticket Settings' }}</h6>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'بادئة التذكرة' : 'Ticket Prefix' }}:</strong> {{ $department->sequence_prefix }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'بادئة الطابور' : 'Queue Prefix' }}:</strong> {{ $department->queue_prefix ?? 'Q' }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'التسلسل الحالي' : 'Current Sequence' }}:</strong> {{ number_format($sequence->sequence_counter) }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'سنة التسلسل' : 'Sequence Year' }}:</strong> {{ $sequence->sequence_year }}</p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'صيغة التذكرة' : 'Ticket Format' }}:</strong> <code>{{ $department->sequence_prefix }}{{ str_pad($sequence->sequence_counter + 1, 8, '0', STR_PAD_LEFT) }}</code></p>
                <p><strong>{{ app()->getLocale() === 'ar' ? 'صيغة الطابور' : 'Queue Format' }}:</strong> <code>{{ $department->queue_prefix ?? 'Q' }}{{ str_pad(1, 4, '0', STR_PAD_LEFT) }}</code></p>
                <p class="text-muted small"><i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'التسلسل مشترك بين جميع الأقسام التي تستخدم نفس البادئة' : 'Sequence is shared among all departments using the same prefix' }}</p>
                
                <div class="mt-3 d-grid gap-2">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ticketSettingsModal">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل إعدادات التذاكر' : 'Edit Ticket Settings' }}
                    </button>
                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'تعديل القسم' : 'Edit Department' }}
                    </a>
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Services -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-task"></i> {{ app()->getLocale() === 'ar' ? 'الخدمات' : 'Services' }}</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة خدمة' : 'Add Service' }}
                </button>
            </div>
            <div class="card-body">
                @if($department->services->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'ar' ? 'اسم الخدمة' : 'Service Name' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($department->services as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->name_ar }}</td>
                                <td><strong>{{ \App\Models\Setting::formatCurrency($service->price) }}</strong></td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editServiceModal{{ $service->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('departments.services.destroy', $service) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Service Modal -->
                            <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('departments.services.update', $service) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ app()->getLocale() === 'ar' ? 'تعديل الخدمة' : 'Edit Service' }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الخدمة' : 'Service Name' }}</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $service->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }}</label>
                                                    <input type="text" class="form-control" name="name_ar" value="{{ $service->name_ar }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</label>
                                                    <input type="number" step="0.01" class="form-control" name="price" value="{{ $service->price }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاختصار' : 'Shortcut' }}</label>
                                                    <input type="text" class="form-control" name="shortcut" value="{{ $service->shortcut }}" placeholder="e.g., CBC, XRAY, MRI">
                                                    <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'استخدم هذا الاختصار للحجز السريع' : 'Use this shortcut for quick booking' }}</small>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                                                <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'تحديث' : 'Update' }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">{{ app()->getLocale() === 'ar' ? 'لا توجد خدمات' : 'No services' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('departments.services.add', $department) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ app()->getLocale() === 'ar' ? 'إضافة خدمة جديدة' : 'Add New Service' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم الخدمة' : 'Service Name' }}</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }}</label>
                        <input type="text" class="form-control" name="name_ar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</label>
                        <input type="number" step="0.01" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاختصار' : 'Shortcut' }}</label>
                        <input type="text" class="form-control" name="shortcut" placeholder="e.g., CBC, XRAY, MRI">
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'استخدم هذا الاختصار للحجز السريع' : 'Use this shortcut for quick booking' }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'إضافة' : 'Add' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ticket Settings Modal -->
<div class="modal fade" id="ticketSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('departments.ticket-settings', $department) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ app()->getLocale() === 'ar' ? 'إعدادات التذاكر' : 'Ticket Settings' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة التذكرة' : 'Ticket Prefix' }}</label>
                        <input type="text" class="form-control" name="sequence_prefix" value="{{ $department->sequence_prefix }}" maxlength="2" required>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان فقط - مشترك بين الأقسام (مثال: TK, OP, ER)' : 'Exactly 2 chars - shared among depts (e.g., TK, OP, ER)' }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'بادئة الطابور' : 'Queue Prefix' }}</label>
                        <input type="text" class="form-control" name="queue_prefix" value="{{ $department->queue_prefix ?? 'Q' }}" maxlength="2" required>
                        <small class="text-muted">{{ app()->getLocale() === 'ar' ? 'حرفان فقط - فريد لكل قسم (مثال: Q1, Q2, X5)' : 'Exactly 2 chars - unique per dept (e.g., Q1, Q2, X5)' }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'صيغة الأرقام' : 'Number Formats' }}</label>
                        <div class="alert alert-info mb-0">
                            <small><strong>{{ app()->getLocale() === 'ar' ? 'التذكرة' : 'Ticket' }}:</strong></small> <code>{{ $department->sequence_prefix ?? 'TK' }}00000001</code> ({{ app()->getLocale() === 'ar' ? 'سنوي مشترك' : 'yearly, shared' }})<br>
                            <small><strong>{{ app()->getLocale() === 'ar' ? 'الطابور' : 'Queue' }}:</strong></small> <code>{{ $department->queue_prefix ?? 'Q' }}0001</code> ({{ app()->getLocale() === 'ar' ? 'يومي لكل قسم' : 'daily, per-dept' }})
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'إعادة تعيين التسلسل' : 'Reset Sequence' }}</label>
                        <button type="button" class="btn btn-warning btn-sm" onclick="resetSequence()">
                            <i class="bi bi-arrow-counterclockwise"></i> {{ app()->getLocale() === 'ar' ? 'تصفير العداد' : 'Reset Counter' }}
                        </button>
                        <input type="hidden" name="reset_sequence" id="reset_sequence" value="0">
                        <small class="text-muted d-block mt-2">{{ app()->getLocale() === 'ar' ? 'يتم التعديل تلقائياً كل سنة' : 'Resets automatically every year' }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetSequence() {
    if (confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تصفير العداد؟' : 'Are you sure you want to reset the counter?' }}')) {
        document.getElementById('reset_sequence').value = '1';
    }
}
</script>
@endpush
@endsection
