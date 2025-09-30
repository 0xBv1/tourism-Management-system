<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BookingFilePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'booking-files.list',
            'booking-files.create',
            'booking-files.edit',
            'booking-files.delete',
            'booking-files.restore',
            'booking-files.show',
            'booking-files.update',
            'booking-files.download',
            'booking-files.send',
            'booking-files.checklist',
            'booking-files.status-update',
            'booking-files.auto-generate-file-no',
            'booking-files.audit-log',
            'booking-files.attachments',
            'booking-files.workflow'
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

        // Grant limited permissions to Reservation role
        if ($reservationRole = Role::whereName('Reservation')->first()) {
            $reservationPermissions = [
                'booking-files.list',
                'booking-files.show',
                'booking-files.edit',
                'booking-files.update',
                'booking-files.checklist',
                'booking-files.status-update',
                'booking-files.attachments',
                'booking-files.workflow'
            ];
            
            $reservationPermissionsDb = [];
            foreach ($reservationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $reservationPermissionsDb[] = $perm->id;
                }
            }
            
            $reservationRole->givePermissionTo($reservationPermissionsDb);
        }

        // Grant limited permissions to Operation role
        if ($operationRole = Role::whereName('Operator')->first()) {
            $operationPermissions = [
                'booking-files.list',
                'booking-files.show',
                'booking-files.edit',
                'booking-files.update',
                'booking-files.checklist',
                'booking-files.status-update',
                'booking-files.attachments',
                'booking-files.workflow'
            ];
            
            $operationPermissionsDb = [];
            foreach ($operationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $operationPermissionsDb[] = $perm->id;
                }
            }
            
            $operationRole->givePermissionTo($operationPermissionsDb);
        }

        // Grant full permissions to Finance role
        if ($financeRole = Role::whereName('Finance')->first()) {
            $financePermissions = [
                'booking-files.list',
                'booking-files.show',
                'booking-files.download',
                'booking-files.audit-log',
                'booking-files.attachments'
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
