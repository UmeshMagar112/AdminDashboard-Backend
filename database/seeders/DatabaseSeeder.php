<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class ,
            AdminUserSeeder::class ,
            CategorySeeder::class ,
            CustomerSeeder::class ,
            ProductSeeder::class,
            OrderSeeder::class, 
            ProductAndInventorySeeder::class,

        ]);
    }
}
