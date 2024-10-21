<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view all orders']);
        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'update orders']);
        Permission::create(['name' => 'delete orders']);

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view all orders', 'create orders', 'view orders', 'update orders', 'delete orders']);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(['view orders', 'update orders']);

        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo(['create orders', 'view orders']);
    }
}
