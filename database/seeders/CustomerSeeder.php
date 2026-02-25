<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'sanctum';
        $customerRole = Role::findByName('customer', $guard);

        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Alex Johnson', 'email' => 'alex@example.com'],
        ];

        foreach ($customers as $idx => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'phone'    => '+977-98000000' . ($idx + 3),
                    'status'   => 1,
                ],
            );

            if (!$user->hasRole($customerRole)) {
                $user->assignRole($customerRole);
            }
        }
    }
}

