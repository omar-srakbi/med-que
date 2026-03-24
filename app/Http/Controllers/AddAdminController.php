<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddAdminController extends Controller
{
    public function index()
    {
        // Check if there are any admins already
        $adminRole = Role::where('name', 'Admin')->first();
        $existingAdmins = $adminRole ? User::where('role_id', $adminRole->id)->count() : 0;
        
        return view('add-admin.index', compact('existingAdmins', 'adminRole'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        
        if (!$adminRole) {
            return back()->withErrors(['error' => 'Admin role not found. Please run database seeder first.']);
        }

        User::create([
            'role_id' => $adminRole->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        return redirect()->route('add-admin.index')
            ->with('success', 'Admin user created successfully! You can now login.');
    }
}
