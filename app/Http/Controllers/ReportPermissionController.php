<?php

namespace App\Http\Controllers;

use App\Models\CustomReport;
use App\Models\ReportPermission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ReportPermissionController extends Controller
{
    public function index(CustomReport $report)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        $permissions = ReportPermission::where('report_id', $report->id)
            ->with(['user', 'role'])
            ->latest()
            ->get();

        $users = User::where('is_active', true)
            ->where('id', '!=', auth()->id())
            ->with('role')
            ->orderBy('first_name')
            ->get();

        $roles = Role::where('is_active', true)->orderBy('name')->get();

        return view('reports.permissions.index', compact('report', 'permissions', 'users', 'roles'));
    }

    public function store(Request $request, CustomReport $report)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'can_view' => 'required|boolean',
            'can_edit' => 'required|boolean',
            'can_delete' => 'required|boolean',
            'can_export' => 'required|boolean',
        ], [
            'user_id.exists' => app()->getLocale() === 'ar' ? 'المستخدم غير موجود' : 'User not found',
            'role_id.exists' => app()->getLocale() === 'ar' ? 'الدور غير موجود' : 'Role not found',
            'can_view.required' => app()->getLocale() === 'ar' ? 'حقل العرض مطلوب' : 'View permission is required',
            'can_export.required' => app()->getLocale() === 'ar' ? 'حقل التصدير مطلوب' : 'Export permission is required',
        ]);

        // Ensure at least one of user_id or role_id is provided
        if (empty($validated['user_id']) && empty($validated['role_id'])) {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'يجب اختيار مستخدم أو دور واحد على الأقل' 
                : 'You must select at least a user or a role']);
        }

        // Check if permission already exists for this user/role
        $existingPermission = ReportPermission::where('report_id', $report->id)
            ->where(function($q) use ($validated) {
                if (!empty($validated['user_id'])) {
                    $q->where('user_id', $validated['user_id']);
                }
                if (!empty($validated['role_id'])) {
                    $q->orWhere('role_id', $validated['role_id']);
                }
            })
            ->first();

        if ($existingPermission) {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'هذه الصلاحية موجودة بالفعل' 
                : 'This permission already exists']);
        }

        ReportPermission::create([
            'report_id' => $report->id,
            'user_id' => $validated['user_id'] ?? null,
            'role_id' => $validated['role_id'] ?? null,
            'can_view' => (bool) $validated['can_view'],
            'can_edit' => (bool) $validated['can_edit'],
            'can_delete' => (bool) $validated['can_delete'],
            'can_export' => (bool) $validated['can_export'],
        ]);

        return back()->with('success', app()->getLocale() === 'ar' 
            ? 'تم إضافة الصلاحية بنجاح' 
            : 'Permission added successfully');
    }

    public function update(Request $request, CustomReport $report, ReportPermission $permission)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        if ($permission->report_id !== $report->id) {
            abort(404);
        }

        $validated = $request->validate([
            'can_view' => 'required|boolean',
            'can_edit' => 'required|boolean',
            'can_delete' => 'required|boolean',
            'can_export' => 'required|boolean',
        ]);

        $permission->update([
            'can_view' => (bool) $validated['can_view'],
            'can_edit' => (bool) $validated['can_edit'],
            'can_delete' => (bool) $validated['can_delete'],
            'can_export' => (bool) $validated['can_export'],
        ]);

        return back()->with('success', app()->getLocale() === 'ar' 
            ? 'تم تحديث الصلاحية بنجاح' 
            : 'Permission updated successfully');
    }

    public function destroy(CustomReport $report, ReportPermission $permission)
    {
        if (!$report->canEdit(auth()->user())) {
            abort(403, 'Unauthorized');
        }

        if ($permission->report_id !== $report->id) {
            abort(404);
        }

        $permission->delete();

        return back()->with('success', app()->getLocale() === 'ar' 
            ? 'تم حذف الصلاحية بنجاح' 
            : 'Permission deleted successfully');
    }
}
