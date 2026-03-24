<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all existing roles first
        Role::truncate();
        
        $roles = [
            // System roles with predefined permissions
            ['name' => 'Admin', 'name_ar' => 'مدير النظام', 'is_system' => true, 'permissions' => ['*'], 'sort_order' => 0],
            
            // Medical staff
            ['name' => 'Doctor', 'name_ar' => 'طبيب', 'is_system' => true, 'permissions' => ['view_patients', 'view_medical_records', 'manage_medical_records'], 'sort_order' => 1],
            ['name' => 'Nurse', 'name_ar' => 'ممرض', 'is_system' => true, 'permissions' => ['view_patients', 'view_medical_records', 'manage_medical_records'], 'sort_order' => 2],
            ['name' => 'Lab Technician', 'name_ar' => 'فني مختبر', 'is_system' => true, 'permissions' => ['view_patients', 'view_medical_records', 'manage_medical_records'], 'sort_order' => 3],
            ['name' => 'Radiology Tech', 'name_ar' => 'فني أشعة', 'is_system' => true, 'permissions' => ['view_patients', 'view_medical_records', 'manage_medical_records'], 'sort_order' => 4],
            
            // Front desk
            ['name' => 'Receptionist', 'name_ar' => 'موظف استقبال', 'is_system' => true, 'permissions' => ['view_patients', 'manage_patients'], 'sort_order' => 5],
            
            // Cashiers
            ['name' => 'Cashier', 'name_ar' => 'أمين صندوق', 'is_system' => true, 'permissions' => ['view_patients', 'manage_patients', 'create_tickets', 'create_payments', 'manage_settings'], 'sort_order' => 6],
            ['name' => 'Head Cashier', 'name_ar' => 'أمين صندوق رئيسي', 'is_system' => true, 'permissions' => ['view_patients', 'manage_patients', 'create_tickets', 'delete_tickets', 'create_advance_tickets', 'create_payments', 'manage_settings'], 'sort_order' => 7],
            
            // Patient role (limited access)
            ['name' => 'Patient', 'name_ar' => 'مريض', 'is_system' => true, 'permissions' => [], 'sort_order' => 8],
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'],
                'name_ar' => $role['name_ar'],
                'is_system' => $role['is_system'],
                'permissions' => $role['permissions'],
                'sort_order' => $role['sort_order'],
            ]);
        }
    }
}
