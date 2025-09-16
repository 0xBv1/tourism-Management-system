<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupplierRolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define canonical supplier.* permissions
        $permissions = [
            // Profile & wallet & stats
            'supplier.profile.view',
            'supplier.profile.edit',
            'supplier.wallet.view',
            'supplier.statistics.view',

            // Services: Hotels
            'supplier.hotels.list',
            'supplier.hotels.create',
            'supplier.hotels.edit',
            'supplier.hotels.delete',

            // Services: Trips
            'supplier.trips.list',
            'supplier.trips.create',
            'supplier.trips.edit',
            'supplier.trips.delete',

            // Services: Tours
            'supplier.tours.list',
            'supplier.tours.create',
            'supplier.tours.edit',
            'supplier.tours.delete',

            // Services: Rooms
            'supplier.rooms.list',
            'supplier.rooms.create',
            'supplier.rooms.edit',
            'supplier.rooms.delete',
            'supplier.rooms.view',

        ];

        // Create/ensure permissions
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Remove legacy permissions previously created by earlier seeders
        $legacy = [
            'view_supplier_profile','edit_supplier_profile','view_supplier_services','create_supplier_services','edit_supplier_services','delete_supplier_services','view_supplier_bookings','manage_supplier_bookings','view_supplier_wallet','manage_supplier_wallet','view_supplier_statistics','view_all_suppliers','create_suppliers','edit_suppliers','delete_suppliers','approve_supplier_services','reject_supplier_services','manage_supplier_commissions','view_supplier_analytics',
            'supplier-services.list','supplier-services.edit'
        ];
        foreach ($legacy as $name) {
            if ($perm = Permission::where('name', $name)->first()) {
                $perm->delete();
            }
        }

        // Roles
        $supplierAdminRole = Role::firstOrCreate(['name' => 'Supplier Admin', 'guard_name' => 'web']);
        $supplierRole = Role::firstOrCreate(['name' => 'Supplier', 'guard_name' => 'web']);

        // Assign permissions
        $supplierAdminRole->syncPermissions($permissions);

        $supplierPermissions = [
            'supplier.profile.view','supplier.profile.edit','supplier.wallet.view','supplier.statistics.view',
            'supplier.hotels.list','supplier.hotels.create','supplier.hotels.edit','supplier.hotels.delete',
            'supplier.rooms.list','supplier.rooms.create','supplier.rooms.edit','supplier.rooms.delete','supplier.rooms.view',
            'supplier.trips.list','supplier.trips.create','supplier.trips.edit','supplier.trips.delete',
            'supplier.tours.list','supplier.tours.create','supplier.tours.edit','supplier.tours.delete',
        ];
        $supplierRole->syncPermissions($supplierPermissions);

        // Ensure platform Admin/Administrator sees supplier admin controls
        foreach (['Admin','Administrator'] as $adminName) {
            if ($adminRole = Role::where('name', $adminName)->first()) {
                $adminRole->givePermissionTo($permissions);
            }
        }

        $this->command?->info('Supplier roles/permissions synced to supplier.* schema.');
    }
}
