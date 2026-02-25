<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Define the guard used in RolesAndPermissionsSeeder
        $guard = 'sanctum';

        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'phone'    => '+977-9800000001',
                'status'   => 1,
            ]
        );
        
        // Find role specifically for the 'sanctum' guard
        $adminRole = Role::findByName('admin', $guard);
        $admin->assignRole($adminRole);

        // Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'     => 'Store Manager',
                'password' => Hash::make('password'),
                'phone'    => '+977-9800000002',
                'status'   => 1,
            ]
        );
        
        // Find role specifically for the 'sanctum' guard
        $managerRole = Role::findByName('manager', $guard);
        $manager->assignRole($managerRole);

        $this->command->info('Admin users seeded!');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role'],
            [
                ['Super Admin',   'admin@example.com',   'password', 'admin'],
                ['Store Manager', 'manager@example.com', 'password', 'manager'],
            ]
        );
    }
}