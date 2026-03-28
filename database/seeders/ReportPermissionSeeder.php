<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class ReportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reportPermissions = [
            'view_reports',
            'create_reports',
            'edit_reports',
            'delete_reports',
            'use_simple_builder',
            'use_advanced_builder',
            'export_reports',
        ];

        // Add to Admin role (all permissions)
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $permissions = array_merge($adminRole->permissions ?? [], $reportPermissions);
            $adminRole->update(['permissions' => array_unique($permissions)]);
        }

        // Add to Head Cashier (view, use_simple_builder, export)
        $headCashier = Role::where('name', 'Head Cashier')->first();
        if ($headCashier) {
            $permissions = array_merge($headCashier->permissions ?? [], [
                'view_reports',
                'use_simple_builder',
                'export_reports',
            ]);
            $headCashier->update(['permissions' => array_unique($permissions)]);
        }

        // Add to Cashier (view, use_simple_builder)
        $cashier = Role::where('name', 'Cashier')->first();
        if ($cashier) {
            $permissions = array_merge($cashier->permissions ?? [], [
                'view_reports',
                'use_simple_builder',
            ]);
            $cashier->update(['permissions' => array_unique($permissions)]);
        }

        // Add to Doctor (view, use_simple_builder)
        $doctor = Role::where('name', 'Doctor')->first();
        if ($doctor) {
            $permissions = array_merge($doctor->permissions ?? [], [
                'view_reports',
                'use_simple_builder',
            ]);
            $doctor->update(['permissions' => array_unique($permissions)]);
        }

        // Add to Receptionist (view, use_simple_builder)
        $receptionist = Role::where('name', 'Receptionist')->first();
        if ($receptionist) {
            $permissions = array_merge($receptionist->permissions ?? [], [
                'view_reports',
                'use_simple_builder',
            ]);
            $receptionist->update(['permissions' => array_unique($permissions)]);
        }
    }
}
