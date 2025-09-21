<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class WorkflowPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'workflow.auto-create-file',
            'workflow.auto-due-date',
            'workflow.sla-timers',
            'workflow.checklists',
            'workflow.templates',
            'workflow.templates.messages',
            'workflow.templates.contracts',
            'workflow.templates.invoices',
            'workflow.notifications',
            'workflow.notifications.email',
            'workflow.notifications.whatsapp',
            'workflow.notifications.sms',
            'workflow.bulk-actions',
            'workflow.inline-edit',
            'workflow.unified-search',
            'workflow.notifications-center',
            'workflow.saved-views',
            'workflow.filters',
            'workflow.audit-log',
            'workflow.attachments',
            'workflow.attachments.upload',
            'workflow.attachments.download',
            'workflow.attachments.delete',
            'workflow.workflow-automation',
            'workflow.status-transitions',
            'workflow.approval-workflows'
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
                'workflow.auto-create-file',
                'workflow.auto-due-date',
                'workflow.sla-timers',
                'workflow.checklists',
                'workflow.templates',
                'workflow.templates.messages',
                'workflow.notifications',
                'workflow.notifications.email',
                'workflow.notifications.whatsapp',
                'workflow.bulk-actions',
                'workflow.inline-edit',
                'workflow.unified-search',
                'workflow.saved-views',
                'workflow.filters',
                'workflow.attachments',
                'workflow.attachments.upload',
                'workflow.attachments.download',
                'workflow.status-transitions'
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
                'workflow.sla-timers',
                'workflow.checklists',
                'workflow.templates',
                'workflow.templates.messages',
                'workflow.notifications',
                'workflow.notifications.email',
                'workflow.bulk-actions',
                'workflow.inline-edit',
                'workflow.unified-search',
                'workflow.saved-views',
                'workflow.filters',
                'workflow.attachments',
                'workflow.attachments.upload',
                'workflow.attachments.download',
                'workflow.status-transitions'
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
                'workflow.sla-timers',
                'workflow.checklists',
                'workflow.templates',
                'workflow.templates.messages',
                'workflow.notifications',
                'workflow.notifications.email',
                'workflow.bulk-actions',
                'workflow.inline-edit',
                'workflow.unified-search',
                'workflow.saved-views',
                'workflow.filters',
                'workflow.attachments',
                'workflow.attachments.upload',
                'workflow.attachments.download',
                'workflow.status-transitions'
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
                'workflow.templates',
                'workflow.templates.contracts',
                'workflow.templates.invoices',
                'workflow.notifications',
                'workflow.notifications.email',
                'workflow.bulk-actions',
                'workflow.unified-search',
                'workflow.saved-views',
                'workflow.filters',
                'workflow.attachments',
                'workflow.attachments.upload',
                'workflow.attachments.download',
                'workflow.audit-log'
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
