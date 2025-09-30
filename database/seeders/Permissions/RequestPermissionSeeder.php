<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RequestPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'requests.list',
            'requests.create',
            'requests.edit',
            'requests.delete',
            'requests.show',
            'requests.respond',
            'requests.close',
            'requests.assign',
            'requests.qa-thread',
            'requests.sla-timer',
            'requests.auto-generate-id'
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

        // Grant full permissions to Sales role
        if ($salesRole = Role::whereName('Sales')->first()) {
            $salesRole->givePermissionTo($permissions_db);
        }

        // Grant response permissions to Reservation role
        if ($reservationRole = Role::whereName('Reservation')->first()) {
            $reservationPermissions = [
                'requests.list',
                'requests.show',
                'requests.respond',
                'requests.close',
                'requests.qa-thread',
                'requests.sla-timer'
            ];
            
            $reservationPermissionsDb = [];
            foreach ($reservationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $reservationPermissionsDb[] = $perm->id;
                }
            }
            
            $reservationRole->givePermissionTo($reservationPermissionsDb);
        }

        // Grant response permissions to Operation role
        if ($operationRole = Role::whereName('Operator')->first()) {
            $operationPermissions = [
                'requests.list',
                'requests.show',
                'requests.respond',
                'requests.close',
                'requests.qa-thread',
                'requests.sla-timer'
            ];
            
            $operationPermissionsDb = [];
            foreach ($operationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $operationPermissionsDb[] = $perm->id;
                }
            }
            
            $operationRole->givePermissionTo($operationPermissionsDb);
        }

        // Grant view-only permissions to Finance role
        if ($financeRole = Role::whereName('Finance')->first()) {
            $financePermissions = [
                'requests.list',
                'requests.show'
            ];
            
            $financePermissionsDb = [];
            foreach ($financePermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $financePermissionsDb[] = $perm->id;
                }
            }
            
            $financeRole->givePermissionTo($financePermissionsDb);
        }
    }
}
