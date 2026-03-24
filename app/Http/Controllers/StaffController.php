<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('role')) {
            $query->where('role_id', $request->role);
        }
        
        $staff = $query->latest()->paginate(15);
        $roles = Role::all();
        
        return view('staff.index', compact('staff', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        
        User::create($validated);
        
        return redirect()->route('staff.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم إضافة الموظف بنجاح' : 'Staff added successfully');
    }

    public function show(User $staff)
    {
        $staff->load('role');
        return view('staff.show', compact('staff'));
    }

    public function edit(User $staff)
    {
        $roles = Role::all();
        return view('staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $staff->update($validated);
        
        return redirect()->route('staff.show', $staff)
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث بيانات الموظف بنجاح' : 'Staff updated successfully');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->withErrors(['error' => app()->getLocale() === 'ar' 
                ? 'لا يمكنك حذف حسابك الخاص' 
                : 'You cannot delete your own account']);
        }
        
        $staff->delete();
        
        return redirect()->route('staff.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الموظف بنجاح' : 'Staff deleted successfully');
    }
}
