<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CheckAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found!');
            return;
        }
        
        $admin = User::where('email', 'admin@example.com')->first();
        
        if ($admin) {
            $this->command->info('Admin user found!');
            $this->command->info('Email: ' . $admin->email);
            $this->command->info('Role ID: ' . $admin->role_id);
            $this->command->info('Name: ' . $admin->first_name . ' ' . $admin->last_name);
            
            // Update password to ensure it's correct
            $admin->update(['password' => Hash::make('password')]);
            $this->command->info('Password reset to: password');
        } else {
            $this->command->info('Creating admin user...');
            User::create([
                'role_id' => $adminRole->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'is_active' => true,
            ]);
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@example.com');
            $this->command->info('Password: password');
        }
    }
}
