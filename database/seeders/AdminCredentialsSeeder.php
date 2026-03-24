<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCredentialsSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $cashierRole = Role::where('name', 'Cashier')->first();
        $headCashierRole = Role::where('name', 'Head Cashier')->first();
        $doctorRole = Role::where('name', 'Doctor')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found!');
            return;
        }
        
        // Create additional admin user
        User::updateOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Admin',
                'last_name' => 'Two',
                'email' => 'admin2@example.com',
                'password' => Hash::make('admin123'),
                'phone' => '0987654321',
                'is_active' => true,
            ]
        );
        
        // Create cashier user
        if ($cashierRole) {
            User::updateOrCreate(
                ['email' => 'cashier@example.com'],
                [
                    'role_id' => $cashierRole->id,
                    'first_name' => 'Cashier',
                    'last_name' => 'User',
                    'email' => 'cashier@example.com',
                    'password' => Hash::make('cashier123'),
                    'phone' => '1111111111',
                    'is_active' => true,
                ]
            );
        }
        
        // Create head cashier user
        if ($headCashierRole) {
            User::updateOrCreate(
                ['email' => 'headcashier@example.com'],
                [
                    'role_id' => $headCashierRole->id,
                    'first_name' => 'Head',
                    'last_name' => 'Cashier',
                    'email' => 'headcashier@example.com',
                    'password' => Hash::make('head123'),
                    'phone' => '2222222222',
                    'is_active' => true,
                ]
            );
        }
        
        // Create doctor user
        if ($doctorRole) {
            User::updateOrCreate(
                ['email' => 'doctor@example.com'],
                [
                    'role_id' => $doctorRole->id,
                    'first_name' => 'Doctor',
                    'last_name' => 'User',
                    'email' => 'doctor@example.com',
                    'password' => Hash::make('doctor123'),
                    'phone' => '3333333333',
                    'is_active' => true,
                ]
            );
        }
        
        $this->command->info('===========================================');
        $this->command->info('       USER CREDENTIALS CREATED');
        $this->command->info('===========================================');
        $this->command->info('Admin 1:');
        $this->command->info('  Email: admin@example.com');
        $this->command->info('  Password: password');
        $this->command->info('');
        $this->command->info('Admin 2:');
        $this->command->info('  Email: admin2@example.com');
        $this->command->info('  Password: admin123');
        $this->command->info('');
        $this->command->info('Cashier:');
        $this->command->info('  Email: cashier@example.com');
        $this->command->info('  Password: cashier123');
        $this->command->info('');
        $this->command->info('Head Cashier:');
        $this->command->info('  Email: headcashier@example.com');
        $this->command->info('  Password: head123');
        $this->command->info('');
        $this->command->info('Doctor:');
        $this->command->info('  Email: doctor@example.com');
        $this->command->info('  Password: doctor123');
        $this->command->info('===========================================');
    }
}
