<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TransportPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = ['transports.list', 'transports.create', 'transports.edit', 'transports.delete'];
        $permissionIds = [];
        foreach ($permissions as $permission) {
            $permissionIds[] = Permission::updateOrCreate(['name' => $permission])->id;
        }

        if ($adminRole = Role::whereName('Administrator')->first()) {
            $adminRole->givePermissionTo($permissionIds);
        }
    }
}
