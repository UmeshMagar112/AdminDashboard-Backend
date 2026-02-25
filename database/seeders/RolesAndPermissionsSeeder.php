<?php
// database/seeders/RolesAndPermissionsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions ────────────────────────────────────────────
        $permissions = [
            // Dashboard
            'view dashboard',

            // Categories
            'view categories', 'create categories', 'edit categories', 'delete categories',

            // Products
            'view products', 'create products', 'edit products', 'delete products',

            // Orders
            'view orders', 'create orders', 'edit orders', 'delete orders',

            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',

            // Coupons
            'view coupons', 'create coupons', 'edit coupons', 'delete coupons',

            // Reviews
            'view reviews', 'edit reviews', 'delete reviews',

            // Inventory
            'view inventory', 'edit inventory', 'adjust inventory',

            // Roles & Users (admin only)
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view admin users', 'create admin users', 'edit admin users', 'delete admin users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // ── Roles ────────────────────────────────────────────────────────────

        // Super Admin — has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $adminRole->syncPermissions(Permission::all());

        // Manager — can do most things except user/role management
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
        $managerRole->syncPermissions([
            'view dashboard',
            'view categories', 'create categories', 'edit categories',
            'view products', 'create products', 'edit products',
            'view orders', 'edit orders',
            'view customers',
            'view coupons', 'create coupons', 'edit coupons',
            'view reviews', 'edit reviews',
            'view inventory', 'edit inventory', 'adjust inventory',
        ]);

        // Customer — regular user, no admin access
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'sanctum']);

        $this->command->info('Roles and permissions seeded!');
    }
}
