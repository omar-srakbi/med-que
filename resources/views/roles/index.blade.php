@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')
@section('page-title', app()->getLocale() === 'ar' ? 'الأدوار' : 'Roles')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-lock"></i> {{ app()->getLocale() === 'ar' ? 'قائمة الأدوار' : 'Role List' }}</span>
        <div>
            <span class="text-muted small me-3"><i class="bi bi-arrows-move"></i> {{ app()->getLocale() === 'ar' ? 'اسحب لترتيب الأدوار' : 'Drag to sort roles' }}</span>
            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة دور' : 'Add Role' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50"><i class="bi bi-arrows-move"></i></th>
                        <th>{{ app()->getLocale() === 'ar' ? 'اسم الدور' : 'Role Name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'عدد الموظفين' : 'Staff Count' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الصلاحيات' : 'Permissions' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody id="roles-sortable">
                    @forelse($roles as $role)
                    <tr data-id="{{ $role->id }}" class="sortable-row" style="cursor: move;">
                        <td><i class="bi bi-arrows-move text-muted"></i></td>
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if($role->is_system)
                                <span class="badge bg-secondary ms-1">{{ app()->getLocale() === 'ar' ? 'نظام' : 'System' }}</span>
                            @endif
                        </td>
                        <td>{{ $role->name_ar }}</td>
                        <td>
                            @if($role->is_system)
                                <span class="badge bg-secondary"><i class="bi bi-lock"></i> {{ app()->getLocale() === 'ar' ? 'نظام' : 'System' }}</span>
                            @else
                                <span class="badge bg-info"><i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'مخصص' : 'Custom' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($role->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-pause-circle"></i> {{ app()->getLocale() === 'ar' ? 'معطل' : 'Disabled' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $role->users_count }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'staff' }}</span>
                        </td>
                        <td>
                            @if($role->permissions && count($role->permissions) > 0)
                                @if(in_array('*', $role->permissions))
                                    <span class="badge bg-success">{{ app()->getLocale() === 'ar' ? 'جميع الصلاحيات' : 'All Permissions' }}</span>
                                @else
                                    <span class="badge bg-warning">{{ count($role->permissions) }} {{ app()->getLocale() === 'ar' ? 'صلاحيات' : 'permissions' }}</span>
                                @endif
                            @else
                                <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا يوجد' : 'None' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($role->name !== 'Admin')
                                <form action="{{ route('roles.toggle-active', $role) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn {{ $role->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                            title="{{ $role->is_active ? (app()->getLocale() === 'ar' ? 'تعطيل' : 'Disable') : (app()->getLocale() === 'ar' ? 'تفعيل' : 'Enable') }}">
                                        <i class="bi bi-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @endif
                                @if($role->users_count == 0)
                                    @if($role->name === 'Admin')
                                    <!-- Special delete for Admin role -->
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adminDeleteModal"
                                            title="{{ app()->getLocale() === 'ar' ? 'حذف دور المدير الأعلى (يتطلب كلمة مرور)' : 'Delete Admin role (requires password)' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @else
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟ سيتم إلغاء تعيين الموظفين المرتبطين بهذا الدور.' : 'Are you sure? Staff assigned to this role will be unassigned.' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف الدور' : 'Delete role' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                @else
                                <!-- Delete even with assigned staff -->
                                    @if($role->name === 'Admin')
                                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adminDeleteModal"
                                            title="{{ app()->getLocale() === 'ar' ? 'حذف دور المدير الأعلى (يتطلب كلمة مرور)' : 'Delete Admin role (requires password)' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @else
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'تحذير: سيتم إلغاء تعيين ' . $role->users_count . ' موظف من هذا الدور. هل أنت متأكد؟' : 'Warning: ' . $role->users_count . ' staff will be unassigned from this role. Are you sure?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف الدور (سيتم إلغاء تعيين الموظفين)' : 'Delete role (staff will be unassigned)' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد أدوار' : 'No roles found' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Admin Delete Modal -->
<div class="modal fade" id="adminDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('roles.destroy-with-password', \App\Models\Role::where('name', 'Admin')->first()) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> {{ app()->getLocale() === 'ar' ? 'تحذير: حذف دور المدير الأعلى' : 'Warning: Delete Super Admin Role' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>{{ app()->getLocale() === 'ar' ? 'هذا الإجراء لا يمكن التراجع عنه!' : 'This action cannot be undone!' }}</strong>
                    </div>
                    <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'لحذف دور المدير الأعلى، يجب إدخال كلمة المرور الخاصة:' : 'To delete the Super Admin role, you must enter the special password:' }}</p>
                    <div class="mb-3">
                        <label class="form-label">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }} <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required autofocus>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-warning">
                        <small><i class="bi bi-info-circle"></i> {{ app()->getLocale() === 'ar' ? 'تأكد من وجود دور مدير آخر قبل حذف هذا الدور' : 'Make sure you have another admin role before deleting this' }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-danger">{{ app()->getLocale() === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('roles-sortable');
    if (el) {
        new Sortable(el, {
            animation: 150,
            handle: '.sortable-row',
            ghostClass: 'bg-light',
            dragClass: 'shadow',
            onEnd: function(evt) {
                const rows = document.querySelectorAll('#roles-sortable .sortable-row');
                const orders = {};
                
                rows.forEach((row, index) => {
                    orders[row.dataset.id] = index;
                });
                
                // Send new order to server
                fetch('{{ route('roles.update-order') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orders: orders })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success toast or message
                        console.log('Order updated successfully');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
});
</script>
@endpush
