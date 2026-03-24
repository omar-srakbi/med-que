<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('sort_order')->get();
        return view('roles.index', compact('roles'));
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
        ]);
        
        foreach ($validated['orders'] as $id => $order) {
            Role::where('id', $id)->update(['sort_order' => $order]);
        }
        
        return response()->json(['success' => true]);
    }

    public function create()
    {
        $availablePermissions = $this->getAvailablePermissions();
        return view('roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'name_ar' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);
        
        $validated['is_system'] = false;
        
        Role::create($validated);
        
        return redirect()->route('roles.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة الدور بنجاح' : 'Role added successfully');
    }

    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $availablePermissions = $this->getAvailablePermissions();
        return view('roles.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system && auth()->user()->role->name !== 'Admin') {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'لا يمكن تعديل أدوار النظام' 
                : 'System roles cannot be edited']);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'name_ar' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);
        
        $role->update($validated);
        
        return redirect()->route('roles.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث الدور بنجاح' : 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        // Admin role requires special password confirmation
        if ($role->name === 'Admin') {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'لحذف دور المدير الأعلى، يرجى استخدام طريقة خاصة مع كلمة المرور' 
                : 'To delete Super Admin role, please use special method with password']);
        }
        
        // Unassign staff from this role (set role_id to NULL)
        $role->users()->update(['role_id' => null]);
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? 'تم حذف الدور وإلغاء تعيين الموظفين المرتبطين به' 
                : 'Role deleted and assigned staff unassigned');
    }

    public function destroyWithPassword(Request $request, Role $role)
    {
        // Only for Admin role
        if ($role->name !== 'Admin') {
            return back()->withErrors(['error' => 'This method is only for Admin role']);
        }
        
        $validated = $request->validate([
            'password' => 'required|string',
        ]);
        
        // Check special password
        if ($validated['password'] !== '1234') {
            return back()->withErrors(['password' => app()->getLocale() === 'ar' 
                ? 'كلمة المرور غير صحيحة' 
                : 'Incorrect password']);
        }
        
        // Unassign staff from Admin role (set role_id to NULL)
        $role->users()->update(['role_id' => null]);
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', app()->getLocale() === 'ar' 
                ? 'تم حذف دور المدير الأعلى وإلغاء تعيين الموظفين' 
                : 'Super Admin role deleted and staff unassigned');
    }

    public function toggleActive(Role $role)
    {
        // Only Admin role cannot be disabled
        if ($role->name === 'Admin') {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'لا يمكن تعطيل دور المدير الأعلى' 
                : 'Cannot disable Super Admin role']);
        }
        
        $role->update(['is_active' => !$role->is_active]);
        
        return back()->with('success', $role->is_active 
            ? (app()->getLocale() === 'ar' ? 'تم تفعيل الدور' : 'Role activated')
            : (app()->getLocale() === 'ar' ? 'تم تعطيل الدور' : 'Role deactivated'));
    }

    public function reassignUsers(Request $request, Role $role)
    {
        if ($role->is_system) {
            return back()->withErrors(['error' => 'Cannot reassign from system role']);
        }
        
        $validated = $request->validate([
            'new_role_id' => 'required|exists:roles,id',
            'user_ids' => 'nullable|array',
        ]);
        
        $userIds = $validated['user_ids'] ?? $role->users->pluck('id')->toArray();
        
        User::whereIn('id', $userIds)->update(['role_id' => $validated['new_role_id']]);
        
        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إعادة تعيين أدوار الموظفين بنجاح' : 'Staff roles reassigned successfully');
    }

    private function getAvailablePermissions()
    {
        return [
            ['value' => '*', 'label' => app()->getLocale() === 'ar' ? 'جميع الصلاحيات' : 'All Permissions', 'description' => app()->getLocale() === 'ar' ? 'وصول كامل للنظام' : 'Full system access'],
            ['value' => 'view_patients', 'label' => app()->getLocale() === 'ar' ? 'عرض المرضى' : 'View Patients', 'description' => app()->getLocale() === 'ar' ? 'عرض بيانات المرضى وسجلاتهم' : 'View patient data and history'],
            ['value' => 'manage_patients', 'label' => app()->getLocale() === 'ar' ? 'إدارة المرضى' : 'Manage Patients', 'description' => app()->getLocale() === 'ar' ? 'إضافة وتعديل بيانات المرضى' : 'Add and edit patient information'],
            ['value' => 'delete_patients', 'label' => app()->getLocale() === 'ar' ? 'حذف المرضى' : 'Delete Patients', 'description' => app()->getLocale() === 'ar' ? 'القدرة على حذف سجلات المرضى' : 'Can delete patient records'],
            ['value' => 'create_tickets', 'label' => app()->getLocale() === 'ar' ? 'إنشاء تذاكر' : 'Create Tickets', 'description' => app()->getLocale() === 'ar' ? 'إنشاء تذاكر لنفس اليوم' : 'Create tickets for same day'],
            ['value' => 'delete_tickets', 'label' => app()->getLocale() === 'ar' ? 'حذف التذاكر' : 'Delete Tickets', 'description' => app()->getLocale() === 'ar' ? 'حذف التذاكر غير المكتملة' : 'Delete incomplete tickets'],
            ['value' => 'delete_completed_tickets', 'label' => app()->getLocale() === 'ar' ? 'حذف التذاكر المكتملة' : 'Delete Completed Tickets', 'description' => app()->getLocale() === 'ar' ? 'حذف التذاكر التي اكتملت' : 'Delete tickets that are completed'],
            ['value' => 'create_advance_tickets', 'label' => app()->getLocale() === 'ar' ? 'حجز تذاكر مستقبلية' : 'Book Future Tickets', 'description' => app()->getLocale() === 'ar' ? 'حجز تذاكر لليوم التالي' : 'Book tickets for the next day'],
            ['value' => 'create_payments', 'label' => app()->getLocale() === 'ar' ? 'إنشاء مدفوعات' : 'Create Payments', 'description' => app()->getLocale() === 'ar' ? 'تسجيل المدفوعات والإيصالات' : 'Record payments and receipts'],
            ['value' => 'view_medical_records', 'label' => app()->getLocale() === 'ar' ? 'عرض السجلات الطبية' : 'View Medical Records', 'description' => app()->getLocale() === 'ar' ? 'عرض السجلات الطبية للمرضى' : 'View patient medical records'],
            ['value' => 'manage_medical_records', 'label' => app()->getLocale() === 'ar' ? 'إدارة السجلات الطبية' : 'Manage Medical Records', 'description' => app()->getLocale() === 'ar' ? 'إنشاء وتعديل السجلات الطبية' : 'Create and edit medical records'],
            ['value' => 'manage_settings', 'label' => app()->getLocale() === 'ar' ? 'إدارة الإعدادات' : 'Manage Settings', 'description' => app()->getLocale() === 'ar' ? 'إعدادات التذاكر والطباعة' : 'Ticket and printing settings'],
            ['value' => 'quick_registration', 'label' => app()->getLocale() === 'ar' ? 'التسجيل السريع' : 'Quick Registration', 'description' => app()->getLocale() === 'ar' ? 'إنشاء مرضى بمعلومات قليلة من خلال التذاكر' : 'Create patients with minimal info from tickets'],
            ['value' => 'complete_patient_profiles', 'label' => app()->getLocale() === 'ar' ? 'إكمال ملفات المرضى' : 'Complete Patient Profiles', 'description' => app()->getLocale() === 'ar' ? 'إكمال معلومات المرضى غير المكتملة' : 'Complete incomplete patient information'],
        ];
    }
}
