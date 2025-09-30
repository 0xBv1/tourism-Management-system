<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdvancedReportsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'reports.inquiries.conversion-funnel',
            'reports.inquiries.status-distribution',
            'reports.inquiries.conversion-rate',
            'reports.booking-files.open-files',
            'reports.booking-files.paid-files',
            'reports.booking-files.overdue-files',
            'reports.booking-files.conversion-time',
            'reports.booking-files.revenue-expected-vs-collected',
            'reports.finance.daily-payments',
            'reports.finance.monthly-payments',
            'reports.finance.remaining-vs-paid',
            'reports.finance.dues-guides',
            'reports.finance.dues-representatives',
            'reports.finance.dues-vehicles',
            'reports.finance.customer-payments',
            'reports.operational.hotels-usage',
            'reports.operational.vehicles-usage',
            'reports.operational.guides-usage',
            'reports.operational.operational-costs',
            'reports.operational.cost-variance',
            'reports.requests.open-closed',
            'reports.requests.response-time',
            'reports.requests.frequent-types',
            'reports.performance.sales-performance',
            'reports.performance.reservation-performance',
            'reports.performance.operation-performance',
            'reports.performance.finance-performance',
            'reports.performance.best-employees',
            'reports.performance.collection-delays',
            'reports.performance.response-delays',
            'reports.advanced.conversion-funnel',
            'reports.advanced.profitability-per-file',
            'reports.advanced.profitability-per-channel',
            'reports.advanced.ar-aging',
            'reports.advanced.collection-effectiveness',
            'reports.advanced.utilization-kpis',
            'reports.advanced.cohort-retention',
            'reports.advanced.variance-report',
            'reports.export.csv',
            'reports.export.excel',
            'reports.export.pdf'
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
                'reports.inquiries.conversion-funnel',
                'reports.inquiries.status-distribution',
                'reports.inquiries.conversion-rate',
                'reports.booking-files.open-files',
                'reports.booking-files.conversion-time',
                'reports.requests.open-closed',
                'reports.requests.response-time',
                'reports.performance.sales-performance',
                'reports.advanced.conversion-funnel',
                'reports.export.csv',
                'reports.export.excel'
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
                'reports.booking-files.open-files',
                'reports.booking-files.paid-files',
                'reports.operational.hotels-usage',
                'reports.operational.vehicles-usage',
                'reports.operational.guides-usage',
                'reports.requests.open-closed',
                'reports.requests.response-time',
                'reports.performance.reservation-performance',
                'reports.export.csv',
                'reports.export.excel'
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
                'reports.operational.hotels-usage',
                'reports.operational.vehicles-usage',
                'reports.operational.guides-usage',
                'reports.operational.operational-costs',
                'reports.operational.cost-variance',
                'reports.requests.open-closed',
                'reports.requests.response-time',
                'reports.performance.operation-performance',
                'reports.advanced.utilization-kpis',
                'reports.advanced.variance-report',
                'reports.export.csv',
                'reports.export.excel'
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
                'reports.finance.daily-payments',
                'reports.finance.monthly-payments',
                'reports.finance.remaining-vs-paid',
                'reports.finance.dues-guides',
                'reports.finance.dues-representatives',
                'reports.finance.dues-vehicles',
                'reports.finance.customer-payments',
                'reports.performance.finance-performance',
                'reports.advanced.ar-aging',
                'reports.advanced.collection-effectiveness',
                'reports.advanced.profitability-per-file',
                'reports.advanced.profitability-per-channel',
                'reports.export.csv',
                'reports.export.excel',
                'reports.export.pdf'
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
