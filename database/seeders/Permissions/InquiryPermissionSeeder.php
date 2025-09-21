<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class InquiryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'inquiries.list',
            'inquiries.create',
            'inquiries.edit',
            'inquiries.delete',
            'inquiries.restore',
            'inquiries.show',
            'inquiries.confirm',
            'inquiries.follow-up',
            'inquiries.cancel',
            'inquiries.assign',
            'inquiries.notes',
            'inquiries.auto-generate-number',
            'inquiries.chats.list',
            'inquiries.chats.create',
            'inquiries.chats.mark-read'
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
                'inquiries.list',
                'inquiries.show',
                'inquiries.notes',
                'inquiries.chats.list',
                'inquiries.chats.create',
                'inquiries.chats.mark-read'
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
        if ($operationRole = Role::whereName('Operation')->first()) {
            $operationPermissions = [
                'inquiries.list',
                'inquiries.show',
                'inquiries.notes',
                'inquiries.chats.list',
                'inquiries.chats.create',
                'inquiries.chats.mark-read'
            ];
            
            $operationPermissionsDb = [];
            foreach ($operationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $operationPermissionsDb[] = $perm->id;
                }
            }
            
            $operationRole->givePermissionTo($operationPermissionsDb);
        }

        // Grant limited permissions to Finance role
        if ($financeRole = Role::whereName('Finance')->first()) {
            $financePermissions = [
                'inquiries.list',
                'inquiries.show',
                'inquiries.chats.list',
                'inquiries.chats.mark-read'
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