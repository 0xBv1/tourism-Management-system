<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class FinancePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'finance.payments.list',
            'finance.payments.create',
            'finance.payments.edit',
            'finance.payments.delete',
            'finance.payments.show',
            'finance.payments.mark-as-paid',
            'finance.payments.partial-payment',
            'finance.payments.refund',
            'finance.payments.credit-note',
            'finance.statements.list',
            'finance.statements.create',
            'finance.statements.edit',
            'finance.statements.show',
            'finance.statements.pay',
            'finance.statements.partial-pay',
            'finance.aging-buckets',
            'finance.cashflow-forecast',
            'finance.dues.manage',
            'finance.dues.guides',
            'finance.dues.representatives',
            'finance.dues.vehicles',
            'finance.reports.financial',
            'finance.reports.collection',
            'finance.reports.profitability',
            'finance.rate-cards.manage',
            'finance.rate-cards.guides',
            'finance.rate-cards.representatives',
            'finance.rate-cards.vehicles'
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

        // Grant full permissions to Finance role
        if ($financeRole = Role::whereName('Finance')->first()) {
            $financeRole->givePermissionTo($permissions_db);
        }

        // Grant limited permissions to Sales role
        if ($salesRole = Role::whereName('Sales')->first()) {
            $salesPermissions = [
                'finance.payments.list',
                'finance.payments.show',
                'finance.statements.list',
                'finance.statements.show',
                'finance.reports.financial'
            ];
            
            $salesPermissionsDb = [];
            foreach ($salesPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $salesPermissionsDb[] = $perm->id;
                }
            }
            
            $salesRole->givePermissionTo($salesPermissionsDb);
        }

        // Grant limited permissions to Reservation role
        if ($reservationRole = Role::whereName('Reservation')->first()) {
            $reservationPermissions = [
                'finance.payments.list',
                'finance.payments.show',
                'finance.statements.list',
                'finance.statements.show'
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
                'finance.payments.list',
                'finance.payments.show',
                'finance.statements.list',
                'finance.statements.show'
            ];
            
            $operationPermissionsDb = [];
            foreach ($operationPermissions as $permission) {
                if ($perm = Permission::whereName($permission)->first()) {
                    $operationPermissionsDb[] = $perm->id;
                }
            }
            
            $operationRole->givePermissionTo($operationPermissionsDb);
        }
    }
}
