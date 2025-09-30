<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MasterDataPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'master-data.guides.list',
            'master-data.guides.create',
            'master-data.guides.edit',
            'master-data.guides.delete',
            'master-data.guides.restore',
            'master-data.guides.show',
            'master-data.guides.activate',
            'master-data.guides.deactivate',
            'master-data.representatives.list',
            'master-data.representatives.create',
            'master-data.representatives.edit',
            'master-data.representatives.delete',
            'master-data.representatives.restore',
            'master-data.representatives.show',
            'master-data.representatives.activate',
            'master-data.representatives.deactivate',
            'master-data.vehicles.list',
            'master-data.vehicles.create',
            'master-data.vehicles.edit',
            'master-data.vehicles.delete',
            'master-data.vehicles.restore',
            'master-data.vehicles.show',
            'master-data.vehicles.activate',
            'master-data.vehicles.deactivate',
            'master-data.vehicles.maintenance',
            'master-data.hotels.list',
            'master-data.hotels.create',
            'master-data.hotels.edit',
            'master-data.hotels.delete',
            'master-data.hotels.restore',
            'master-data.hotels.show',
            'master-data.hotels.activate',
            'master-data.hotels.deactivate',
            'master-data.rate-cards.manage',
            'master-data.rate-cards.guides',
            'master-data.rate-cards.representatives',
            'master-data.rate-cards.vehicles',
            'master-data.rate-cards.hotels'
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

        // Grant limited permissions to Sales role
        if ($salesRole = Role::whereName('Sales')->first()) {
            $salesPermissions = [
                'master-data.guides.list',
                'master-data.guides.show',
                'master-data.representatives.list',
                'master-data.representatives.show',
                'master-data.vehicles.list',
                'master-data.vehicles.show',
                'master-data.hotels.list',
                'master-data.hotels.show'
            ];
            
            $salesPermissionsDb = [];
            foreach ($salesPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $salesPermissionsDb[] = $perm->id;
                }
            }
            
            $salesRole->givePermissionTo($salesPermissionsDb);
        }

        // Grant full permissions to Reservation role
        if ($reservationRole = Role::whereName('Reservation')->first()) {
            $reservationPermissions = [
                'master-data.guides.list',
                'master-data.guides.create',
                'master-data.guides.edit',
                'master-data.guides.show',
                'master-data.guides.activate',
                'master-data.guides.deactivate',
                'master-data.representatives.list',
                'master-data.representatives.create',
                'master-data.representatives.edit',
                'master-data.representatives.show',
                'master-data.representatives.activate',
                'master-data.representatives.deactivate',
                'master-data.vehicles.list',
                'master-data.vehicles.create',
                'master-data.vehicles.edit',
                'master-data.vehicles.show',
                'master-data.vehicles.activate',
                'master-data.vehicles.deactivate',
                'master-data.hotels.list',
                'master-data.hotels.create',
                'master-data.hotels.edit',
                'master-data.hotels.show',
                'master-data.hotels.activate',
                'master-data.hotels.deactivate'
            ];
            
            $reservationPermissionsDb = [];
            foreach ($reservationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $reservationPermissionsDb[] = $perm->id;
                }
            }
            
            $reservationRole->givePermissionTo($reservationPermissionsDb);
        }

        // Grant full permissions to Operation role
        if ($operationRole = Role::whereName('Operator')->first()) {
            $operationPermissions = [
                'master-data.guides.list',
                'master-data.guides.create',
                'master-data.guides.edit',
                'master-data.guides.show',
                'master-data.guides.activate',
                'master-data.guides.deactivate',
                'master-data.representatives.list',
                'master-data.representatives.create',
                'master-data.representatives.edit',
                'master-data.representatives.show',
                'master-data.representatives.activate',
                'master-data.representatives.deactivate',
                'master-data.vehicles.list',
                'master-data.vehicles.create',
                'master-data.vehicles.edit',
                'master-data.vehicles.show',
                'master-data.vehicles.activate',
                'master-data.vehicles.deactivate',
                'master-data.vehicles.maintenance',
                'master-data.hotels.list',
                'master-data.hotels.create',
                'master-data.hotels.edit',
                'master-data.hotels.show',
                'master-data.hotels.activate',
                'master-data.hotels.deactivate'
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
                'master-data.guides.list',
                'master-data.guides.show',
                'master-data.representatives.list',
                'master-data.representatives.show',
                'master-data.vehicles.list',
                'master-data.vehicles.show',
                'master-data.hotels.list',
                'master-data.hotels.show',
                'master-data.rate-cards.manage',
                'master-data.rate-cards.guides',
                'master-data.rate-cards.representatives',
                'master-data.rate-cards.vehicles',
                'master-data.rate-cards.hotels'
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
