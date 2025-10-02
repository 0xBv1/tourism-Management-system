<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class NileCruisePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'nile_cruises.list',
            'nile_cruises.create', 
            'nile_cruises.edit',
            'nile_cruises.delete',
            'nile_cruises.restore',
            'nile_cruises.show',
            'nile_cruises.assign',
            'nile_cruises.unassign',
            'nile_cruises.utilization'
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
                'nile_cruises.list',
                'nile_cruises.show',
                'nile_cruises.edit',
                'nile_cruises.assign',
                'nile_cruises.unassign',
                'nile_cruises.utilization'
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
                'nile_cruises.list',
                'nile_cruises.show',
                'nile_cruises.utilization'
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
