<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageSupplierServicesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:supplier-services {action : Action to perform (create|assign|list)} {--role= : Role name for assignment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage supplier services permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $roleName = $this->option('role');

        switch ($action) {
            case 'create':
                $this->createPermissions();
                break;
            case 'assign':
                if (!$roleName) {
                    $this->error('Role name is required for assignment. Use --role=RoleName');
                    return 1;
                }
                $this->assignPermissions($roleName);
                break;
            case 'list':
                $this->listPermissions();
                break;
            default:
                $this->error('Invalid action. Use: create, assign, or list');
                return 1;
        }

        return 0;
    }

    /**
     * Create supplier services permissions
     */
    private function createPermissions()
    {
        $permissions = [
            'supplier-services.list',
            'supplier-services.edit',
            'supplier-services.approve',
            'supplier-services.reject',
        ];

        $this->info('Creating supplier services permissions...');

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $this->line("✓ Created permission: {$perm->name}");
        }

        $this->info('All permissions created successfully!');
    }

    /**
     * Assign permissions to a role
     */
    private function assignPermissions($roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->error("Role '{$roleName}' not found!");
            return;
        }

        $permissions = [
            'supplier-services.list',
            'supplier-services.edit',
            'supplier-services.approve',
            'supplier-services.reject',
        ];

        $this->info("Assigning permissions to role '{$roleName}'...");

        foreach ($permissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm) {
                $role->givePermissionTo($perm);
                $this->line("✓ Assigned permission '{$permission}' to role '{$roleName}'");
            } else {
                $this->warn("⚠ Permission '{$permission}' not found. Run 'create' action first.");
            }
        }

        $this->info('Permissions assigned successfully!');
    }

    /**
     * List all supplier services permissions
     */
    private function listPermissions()
    {
        $permissions = Permission::where('name', 'like', 'supplier-services.%')->get();

        if ($permissions->isEmpty()) {
            $this->info('No supplier services permissions found.');
            return;
        }

        $this->info('Supplier Services Permissions:');
        $this->table(
            ['ID', 'Name', 'Guard', 'Created At'],
            $permissions->map(function ($permission) {
                return [
                    $permission->id,
                    $permission->name,
                    $permission->guard_name,
                    $permission->created_at->format('Y-m-d H:i:s'),
                ];
            })
        );

        // Show roles with these permissions
        $this->info('Roles with Supplier Services Permissions:');
        $roles = Role::with('permissions')
            ->whereHas('permissions', function ($query) {
                $query->where('name', 'like', 'supplier-services.%');
            })
            ->get();

        if ($roles->isEmpty()) {
            $this->info('No roles have supplier services permissions assigned.');
            return;
        }

        foreach ($roles as $role) {
            $this->line("Role: {$role->name}");
            $rolePermissions = $role->permissions->where('name', 'like', 'supplier-services.%');
            foreach ($rolePermissions as $permission) {
                $this->line("  - {$permission->name}");
            }
            $this->line('');
        }
    }
}
