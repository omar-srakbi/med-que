@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إدارة صلاحيات التقرير' : 'Report Permissions Management')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة الصلاحيات' : 'Manage Permissions')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-shield-lock"></i> 
            {{ app()->getLocale() === 'ar' ? 'صلاحيات تقرير:' : 'Report Permissions:' }} 
            <strong>{{ app()->getLocale() === 'ar' ? $report->name_ar : $report->name_en }}</strong>
        </span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                <i class="bi bi-plus-circle"></i> {{ app()->getLocale() === 'ar' ? 'إضافة صلاحية' : 'Add Permission' }}
            </button>
            <a href="{{ route('reports.builder.show', $report) }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'رجوع للتقرير' : 'Back to Report' }}
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($permissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</th>
                        <th class="text-center">{{ app()->getLocale() === 'ar' ? 'تصدير' : 'Export' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                    <tr>
                        <td>
                            @if($permission->user_id)
                                <span class="badge bg-info">
                                    <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'مستخدم' : 'User' }}
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'دور' : 'Role' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($permission->user_id)
                                {{ $permission->user->full_name ?? 'Unknown User' }}
                                @if($permission->user->role)
                                    <br><small class="text-muted">{{ $permission->user->role->name_ar ?? $permission->user->role->name }}</small>
                                @endif
                            @else
                                {{ $permission->role->name_ar ?? $permission->role->name }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if($permission->can_view)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($permission->can_edit)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($permission->can_delete)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($permission->can_export)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPermissionModal{{ $permission->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('reports.builder.permissions.destroy', [$report, $permission]) }}" 
                                      method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Edit Permission Modal -->
                            <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('reports.builder.permissions.update', [$report, $permission]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ app()->getLocale() === 'ar' ? 'تعديل الصلاحية' : 'Edit Permission' }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>{{ app()->getLocale() === 'ar' ? 'المستخدم/الدور:' : 'User/Role:' }}</strong>
                                                    @if($permission->user_id)
                                                        <div class="mt-1">{{ $permission->user->full_name }}</div>
                                                    @else
                                                        <div class="mt-1">{{ $permission->role->name_ar ?? $permission->role->name }}</div>
                                                    @endif
                                                </div>
                                                <hr>
                                                <div class="form-check mb-2">
                                                    <input type="hidden" name="can_view" value="0">
                                                    <input class="form-check-input" type="checkbox" name="can_view" id="edit_view_{{ $permission->id }}" value="1"
                                                           {{ $permission->can_view ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_view_{{ $permission->id }}">
                                                        <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه العرض' : 'Can View' }}
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="hidden" name="can_edit" value="0">
                                                    <input class="form-check-input" type="checkbox" name="can_edit" id="edit_edit_{{ $permission->id }}" value="1"
                                                           {{ $permission->can_edit ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_edit_{{ $permission->id }}">
                                                        <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه التعديل' : 'Can Edit' }}
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="hidden" name="can_delete" value="0">
                                                    <input class="form-check-input" type="checkbox" name="can_delete" id="edit_delete_{{ $permission->id }}" value="1"
                                                           {{ $permission->can_delete ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_delete_{{ $permission->id }}">
                                                        <i class="bi bi-trash"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه الحذف' : 'Can Delete' }}
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="hidden" name="can_export" value="0">
                                                    <input class="form-check-input" type="checkbox" name="can_export" id="edit_export_{{ $permission->id }}" value="1"
                                                           {{ $permission->can_export ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_export_{{ $permission->id }}">
                                                        <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه التصدير' : 'Can Export' }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-shield-lock" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3">{{ app()->getLocale() === 'ar' ? 'لا توجد صلاحيات' : 'No permissions yet' }}</h5>
            <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'أضف صلاحيات للتحكم في من يمكنه الوصول إلى هذا التقرير' : 'Add permissions to control who can access this report' }}</p>
        </div>
        @endif

        <div class="alert alert-info mt-4">
            <i class="bi bi-info-circle"></i>
            <strong>{{ app()->getLocale() === 'ar' ? 'ملاحظات:' : 'Notes:' }}</strong>
            <ul class="mb-0 mt-2">
                <li>{{ app()->getLocale() === 'ar' ? 'صاحب التقرير يمكنه دائماً الوصول الكامل' : 'Report owner always has full access' }}</li>
                <li>{{ app()->getLocale() === 'ar' ? 'إذا كان التقرير عاماً، يمكن للجميع عرضه' : 'If report is public, everyone can view it' }}</li>
                <li><strong>{{ app()->getLocale() === 'ar' ? 'الأولوية:' : 'Priority:' }}</strong> {{ app()->getLocale() === 'ar' ? 'صلاحيات المستخدم المحددة لها الأولوية على صلاحيات الدور' : 'User-specific permissions take priority over role permissions' }}</li>
                <li>{{ app()->getLocale() === 'ar' ? 'إذا لم توجد صلاحية للمستخدم، يتم استخدام صلاحيات الدور' : 'If no user permission exists, role permissions are used as fallback' }}</li>
            </ul>
        </div>
    </div>
</div>

<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reports.builder.permissions.store', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-shield-lock"></i> 
                        {{ app()->getLocale() === 'ar' ? 'إضافة صلاحية جديدة' : 'Add New Permission' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-person"></i> {{ app()->getLocale() === 'ar' ? 'مستخدم' : 'User' }} 
                            <span class="text-muted">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</span>
                        </label>
                        <select name="user_id" class="form-select">
                            <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر مستخدم' : 'Select a user' }} --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-people"></i> {{ app()->getLocale() === 'ar' ? 'دور' : 'Role' }} 
                            <span class="text-muted">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</span>
                        </label>
                        <select name="role_id" class="form-select">
                            <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر دور' : 'Select a role' }} --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name_ar ?? $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ app()->getLocale() === 'ar' ? 'يجب اختيار مستخدم أو دور واحد على الأقل' : 'You must select at least a user or a role' }}
                    </div>
                    <hr>
                    <h6>{{ app()->getLocale() === 'ar' ? 'الصلاحيات:' : 'Permissions:' }}</h6>
                    <div class="form-check mb-2">
                        <input type="hidden" name="can_view" value="0">
                        <input class="form-check-input" type="checkbox" name="can_view" id="add_view" value="1" checked>
                        <label class="form-check-label" for="add_view">
                            <i class="bi bi-eye"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه العرض' : 'Can View' }}
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="can_edit" value="0">
                        <input class="form-check-input" type="checkbox" name="can_edit" id="add_edit" value="1">
                        <label class="form-check-label" for="add_edit">
                            <i class="bi bi-pencil"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه التعديل' : 'Can Edit' }}
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="can_delete" value="0">
                        <input class="form-check-input" type="checkbox" name="can_delete" id="add_delete" value="1">
                        <label class="form-check-label" for="add_delete">
                            <i class="bi bi-trash"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه الحذف' : 'Can Delete' }}
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="hidden" name="can_export" value="0">
                        <input class="form-check-input" type="checkbox" name="can_export" id="add_export" value="1" checked>
                        <label class="form-check-label" for="add_export">
                            <i class="bi bi-download"></i> {{ app()->getLocale() === 'ar' ? 'يمكنه التصدير' : 'Can Export' }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ app()->getLocale() === 'ar' ? 'إضافة صلاحية' : 'Add Permission' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
