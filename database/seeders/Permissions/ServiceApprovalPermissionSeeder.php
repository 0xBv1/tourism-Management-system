<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class ServiceApprovalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Service Approvals CRUD
            'service-approvals.list',
            'service-approvals.show',
            'service-approvals.approve',
            'service-approvals.reject',
            'service-approvals.update',
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
