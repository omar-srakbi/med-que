<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Clinics', 'name_ar' => 'العيادات', 'description' => 'General and specialized clinics'],
            ['name' => 'Kidney Center', 'name_ar' => 'مركز الكلى', 'description' => 'Kidney diseases and dialysis'],
            ['name' => 'Blood Laboratory', 'name_ar' => 'مختبر الدم', 'description' => 'Blood tests and analysis'],
            ['name' => 'Radiology Center', 'name_ar' => 'مركز الأشعة', 'description' => 'X-ray and imaging services'],
            ['name' => 'MRI', 'name_ar' => 'الرنين المغناطيسي', 'description' => 'Magnetic resonance imaging'],
            ['name' => 'Physiological Treatment', 'name_ar' => 'العلاج الطبيعي', 'description' => 'Physical therapy and rehabilitation'],
        ];

        foreach ($departments as $dept) {
            $department = Department::create([
                'name' => $dept['name'],
                'name_ar' => $dept['name_ar'],
                'description' => $dept['description'],
            ]);

            // Add default services for each department
            Service::create([
                'department_id' => $department->id,
                'name' => 'General ' . $dept['name'] . ' Service',
                'name_ar' => 'خدمة ' . $dept['name_ar'] . ' العامة',
                'price' => 50.00,
            ]);
        }
    }
}
