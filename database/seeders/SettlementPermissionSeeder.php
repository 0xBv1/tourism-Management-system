<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettlementPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'settlements.list',
            'settlements.create',
            'settlements.edit',
            'settlements.delete',
            'settlements.show',
            'settlements.calculate',
            'settlements.approve',
            'settlements.reject',
            'settlements.mark-paid',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'settlements.list',
                'settlements.create',
                'settlements.edit',
                'settlements.show',
                'settlements.calculate',
                'settlements.approve',
                'settlements.reject',
                'settlements.mark-paid',
            ]);
        }

        $accountantRole = Role::where('name', 'accountant')->first();
        if ($accountantRole) {
            $accountantRole->givePermissionTo([
                'settlements.list',
                'settlements.show',
                'settlements.calculate',
                'settlements.approve',
                'settlements.mark-paid',
            ]);
        }

        $this->command->info('Settlement permissions created successfully');
    }
}