<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class FixAdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        
        if ($adminRole) {
            User::where('email', 'admin@example.com')->update(['role_id' => $adminRole->id]);
            $this->command->info('Admin user role updated successfully!');
        } else {
            $this->command->error('Admin role not found!');
        }
    }
}
