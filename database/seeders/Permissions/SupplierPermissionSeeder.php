<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class SupplierPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Supplier CRUD
            'suppliers.list',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',

        ];

        $permissionIds = [];
        foreach ($permissions as $name) {
            $permissionIds[] = Permission::firstOrCreate(['name' => $name])->id;
        }

        // Grant to Administrator role used in this project
        if ($admin = Role::whereName('Administrator')->first()) {
            $admin->givePermissionTo($permissionIds);
        }

        // Also grant to Admin role if present
        if ($adminAlt = Role::whereName('Admin')->first()) {
            $adminAlt->givePermissionTo($permissionIds);
        }
    }
}


