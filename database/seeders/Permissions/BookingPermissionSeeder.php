<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BookingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'bookings.list',
            'bookings.create', 
            'bookings.edit',
            'bookings.delete',
            'bookings.restore',
            'bookings.show',
            'bookings.update',
            'bookings.download',
            'bookings.send',
            'bookings.checklist'
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
                'bookings.list',
                'bookings.show',
                'bookings.update',
                'bookings.download',
                'bookings.send',
                'bookings.checklist'
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
                'bookings.list',
                'bookings.show',
                'bookings.download'
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
