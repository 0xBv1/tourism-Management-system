<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $rolesWithPermissions = [
            'Administrator' => [
                'permissions' => ['users', 'roles'],
                'description' => 'Full system access and management'
            ],
            'Admin' => [
                'permissions' => ['users', 'roles', 'inquiries', 'bookings', 'payments', 'reports'],
                'description' => 'Administrative access to all modules'
            ],
            'Sales' => [
                'permissions' => ['inquiries', 'booking-files', 'requests', 'workflow'],
                'description' => 'Sales management and inquiry handling'
            ],
            'Reservation' => [
                'permissions' => ['inquiries', 'booking-files', 'requests', 'master-data', 'workflow'],
                'description' => 'Reservation management and booking processing'
            ],
            'Operator' => [
                'permissions' => ['inquiries', 'booking-files', 'requests', 'master-data', 'workflow'],
                'description' => 'Operational management and resource coordination'
            ],
            'Finance' => [
                'permissions' => ['inquiries', 'booking-files', 'payments', 'finance', 'reports'],
                'description' => 'Financial management and payment processing'
            ],
            'Editor' => [
                'permissions' => [],
                'description' => 'Content editing access'
            ],
            'Operator' => [
                'permissions' => [],
                'description' => 'Basic operational access'
            ],
        ];

        foreach ($rolesWithPermissions as $role => $config) {
            $permissions = $config['permissions'] ?? [];
            $description = $config['description'] ?? '';

            $dbRole = Role::updateOrCreate([
                'name' => $role
            ], [
                'name' => $role,
                'guard_name' => 'web'
            ]);

            // Create basic CRUD permissions for each module
            foreach ($permissions as $permission) {
                $crudPermissions = [
                    "$permission.list",
                    "$permission.create", 
                    "$permission.edit",
                    "$permission.delete",
                    "$permission.restore",
                    "$permission.show"
                ];

                // Create permissions
                foreach ($crudPermissions as $perm) {
                    Permission::updateOrCreate(['name' => $perm]);
                }

                // Assign permissions based on role
                if (in_array($role, ['Administrator', 'Admin'])) {
                    $dbRole->givePermissionTo($crudPermissions);
                } elseif ($role === 'Sales') {
                    // Sales gets full access to their modules
                    $dbRole->givePermissionTo($crudPermissions);
                } elseif ($role === 'Reservation') {
                    // Reservation gets full access to their modules
                    $dbRole->givePermissionTo($crudPermissions);
                } elseif ($role === 'Operator') {
                    // Operation gets full access to their modules
                    $dbRole->givePermissionTo($crudPermissions);
                } elseif ($role === 'Finance') {
                    // Finance gets full access to their modules
                    $dbRole->givePermissionTo($crudPermissions);
                }
            }

            // Add specific permissions for each role
            $this->assignSpecificPermissions($dbRole, $role);
        }
    }

    private function assignSpecificPermissions($role, $roleName)
    {
        $specificPermissions = [];

        switch ($roleName) {
            case 'Administrator':
                $specificPermissions = [
                    'settings.show', 'settings.edit',
                    'media.access',
                    'redirect-rules.list', 'redirect-rules.create', 'redirect-rules.edit', 'redirect-rules.delete', 'redirect-rules.restore', 'redirect-rules.export', 'redirect-rules.import'
                ];
                break;

            case 'Admin':
                $specificPermissions = [
                    'settings.show', 'settings.edit',
                    'media.access',
                    'inquiries.chats.list', 'inquiries.chats.create', 'inquiries.chats.mark-read',
                    'reports.index', 'reports.inquiries', 'reports.bookings', 'reports.finance', 'reports.operational', 'reports.performance', 'reports.export'
                ];
                break;

            case 'Sales':
                $specificPermissions = [
                    'inquiries.chats.list', 'inquiries.chats.create', 'inquiries.chats.mark-read',
                    'inquiries.confirm', 'inquiries.follow-up', 'inquiries.cancel', 'inquiries.assign', 'inquiries.notes', 'inquiries.auto-generate-number',
                    'reports.inquiries', 'reports.performance.sales-performance'
                ];
                break;

            case 'Reservation':
                $specificPermissions = [
                    'inquiries.chats.list', 'inquiries.chats.create', 'inquiries.chats.mark-read',
                    'inquiries.notes',
                    'master-data.guides.list', 'master-data.guides.create', 'master-data.guides.edit', 'master-data.guides.show', 'master-data.guides.activate', 'master-data.guides.deactivate',
                    'master-data.representatives.list', 'master-data.representatives.create', 'master-data.representatives.edit', 'master-data.representatives.show', 'master-data.representatives.activate', 'master-data.representatives.deactivate',
                    'master-data.vehicles.list', 'master-data.vehicles.create', 'master-data.vehicles.edit', 'master-data.vehicles.show', 'master-data.vehicles.activate', 'master-data.vehicles.deactivate',
                    'master-data.hotels.list', 'master-data.hotels.create', 'master-data.hotels.edit', 'master-data.hotels.show', 'master-data.hotels.activate', 'master-data.hotels.deactivate',
                    'reports.operational.hotels-usage', 'reports.operational.vehicles-usage', 'reports.operational.guides-usage'
                ];
                break;

            case 'Operator':
                $specificPermissions = [
                    'inquiries.chats.list', 'inquiries.chats.create', 'inquiries.chats.mark-read',
                    'inquiries.notes',
                    'master-data.guides.list', 'master-data.guides.create', 'master-data.guides.edit', 'master-data.guides.show', 'master-data.guides.activate', 'master-data.guides.deactivate',
                    'master-data.representatives.list', 'master-data.representatives.create', 'master-data.representatives.edit', 'master-data.representatives.show', 'master-data.representatives.activate', 'master-data.representatives.deactivate',
                    'master-data.vehicles.list', 'master-data.vehicles.create', 'master-data.vehicles.edit', 'master-data.vehicles.show', 'master-data.vehicles.activate', 'master-data.vehicles.deactivate', 'master-data.vehicles.maintenance',
                    'master-data.hotels.list', 'master-data.hotels.create', 'master-data.hotels.edit', 'master-data.hotels.show', 'master-data.hotels.activate', 'master-data.hotels.deactivate',
                    'reports.operational.hotels-usage', 'reports.operational.vehicles-usage', 'reports.operational.guides-usage', 'reports.operational.operational-costs', 'reports.operational.cost-variance'
                ];
                break;

            case 'Finance':
                $specificPermissions = [
                    'inquiries.chats.list', 'inquiries.chats.mark-read',
                    'finance.payments.list', 'finance.payments.create', 'finance.payments.edit', 'finance.payments.delete', 'finance.payments.show', 'finance.payments.mark-as-paid', 'finance.payments.partial-payment', 'finance.payments.refund', 'finance.payments.credit-note',
                    'finance.statements.list', 'finance.statements.create', 'finance.statements.edit', 'finance.statements.show', 'finance.statements.pay', 'finance.statements.partial-pay',
                    'finance.aging-buckets', 'finance.cashflow-forecast', 'finance.dues.manage', 'finance.dues.guides', 'finance.dues.representatives', 'finance.dues.vehicles',
                    'finance.reports.financial', 'finance.reports.collection', 'finance.reports.profitability',
                    'finance.rate-cards.manage', 'finance.rate-cards.guides', 'finance.rate-cards.representatives', 'finance.rate-cards.vehicles',
                    'master-data.rate-cards.manage', 'master-data.rate-cards.guides', 'master-data.rate-cards.representatives', 'master-data.rate-cards.vehicles', 'master-data.rate-cards.hotels',
                    'reports.finance', 'reports.performance.finance-performance'
                ];
                break;
        }

        // Create and assign specific permissions
        foreach ($specificPermissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        if (!empty($specificPermissions)) {
            $role->givePermissionTo($specificPermissions);
        }
    }
}