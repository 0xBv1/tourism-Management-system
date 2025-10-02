<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TicketPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'tickets.list',
            'tickets.create', 
            'tickets.edit',
            'tickets.delete',
            'tickets.restore',
            'tickets.show',
            'tickets.assign',
            'tickets.unassign',
            'tickets.utilization'
        ];
        
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }

        // Grant to Administrator role
        if ($adminRole = Role::whereName('Administrator')->first()) {
            $adminRole->givePermissionTo($permissions_db);
        }

        // Also grant to Admin role if present
        if ($adminAlt = Role::whereName('Admin')->first()) {
            $adminAlt->givePermissionTo($permissions_db);
        }

        // Grant limited permissions to Manager role if present
        if ($managerRole = Role::whereName('Manager')->first()) {
            $managerPermissions = [
                'tickets.list',
                'tickets.show',
                'tickets.edit',
                'tickets.assign',
                'tickets.unassign',
                'tickets.utilization'
            ];
            
            $managerPermissionsDb = [];
            foreach ($managerPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $managerPermissionsDb[] = $perm->id;
                }
            }
            
            $managerRole->givePermissionTo($managerPermissionsDb);
        }

        // Grant view-only permissions to Staff role if present
        if ($staffRole = Role::whereName('Staff')->first()) {
            $staffPermissions = [
                'tickets.list',
                'tickets.show',
                'tickets.utilization'
            ];
            
            $staffPermissionsDb = [];
            foreach ($staffPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $staffPermissionsDb[] = $perm->id;
                }
            }
            
            $staffRole->givePermissionTo($staffPermissionsDb);
        }
    }
}
