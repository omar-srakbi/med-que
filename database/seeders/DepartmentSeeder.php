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
            ['name' => 'Clinics', 'name_ar' => 'العيادات', 'description' => 'General and specialized clinics', 'queue_prefix' => 'Q1', 'sequence_prefix' => 'TK'],
            ['name' => 'Kidney Center', 'name_ar' => 'مركز الكلى', 'description' => 'Kidney diseases and dialysis', 'queue_prefix' => 'Q2', 'sequence_prefix' => 'TK'],
            ['name' => 'Blood Laboratory', 'name_ar' => 'مختبر الدم', 'description' => 'Blood tests and analysis', 'queue_prefix' => 'Q3', 'sequence_prefix' => 'TK'],
            ['name' => 'Radiology Center', 'name_ar' => 'مركز الأشعة', 'description' => 'X-ray and imaging services', 'queue_prefix' => 'Q4', 'sequence_prefix' => 'TK'],
            ['name' => 'MRI', 'name_ar' => 'الرنين المغناطيسي', 'description' => 'Magnetic resonance imaging', 'queue_prefix' => 'Q5', 'sequence_prefix' => 'TK'],
            ['name' => 'Physiological Treatment', 'name_ar' => 'العلاج الطبيعي', 'description' => 'Physical therapy and rehabilitation', 'queue_prefix' => 'Q6', 'sequence_prefix' => 'TK'],
        ];

        foreach ($departments as $dept) {
            $department = Department::create([
                'name' => $dept['name'],
                'name_ar' => $dept['name_ar'],
                'description' => $dept['description'],
                'queue_prefix' => $dept['queue_prefix'],
                'sequence_prefix' => $dept['sequence_prefix'],
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
