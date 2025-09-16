<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RollbackSupplierModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier:rollback {--force : Force rollback without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the supplier module migrations and clean up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will rollback all supplier module migrations and remove supplier data. Are you sure?')) {
                $this->info('Rollback cancelled.');
                return 0;
            }
        }

        $this->info('Starting supplier module rollback...');

        try {
            // Step 1: Rollback migrations
            $this->info('Rolling back migrations...');
            $this->rollbackMigrations();

            // Step 2: Clean up roles and permissions
            $this->info('Cleaning up roles and permissions...');
            $this->cleanupRolesAndPermissions();

            // Step 3: Clean up any remaining data
            $this->info('Cleaning up remaining data...');
            $this->cleanupData();

            $this->info('Supplier module rollback completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Rollback failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Rollback supplier-related migrations.
     */
    private function rollbackMigrations()
    {
        // List of supplier-related migration files to rollback
        $migrations = [
            '2025_08_25_202931_create_supplier_transport_bookings_table.php',
            '2025_08_25_202907_create_supplier_tour_bookings_table.php',
            '2025_08_25_202809_create_supplier_hotel_bookings_table.php',
            '2025_08_25_202316_create_suppliers_table.php',
        ];

        foreach ($migrations as $migration) {
            $this->info("Rolling back: {$migration}");
            
            // Find the migration in the migrations table
            $migrationRecord = DB::table('migrations')
                ->where('migration', 'like', '%' . str_replace('.php', '', $migration))
                ->first();

            if ($migrationRecord) {
                // Rollback this specific migration
                Artisan::call('migrate:rollback', [
                    '--step' => 1,
                    '--path' => 'database/migrations/' . $migration
                ]);
                
                $this->info("Rolled back: {$migration}");
            } else {
                $this->warn("Migration not found: {$migration}");
            }
        }
    }

    /**
     * Clean up roles and permissions.
     */
    private function cleanupRolesAndPermissions()
    {
        // Remove supplier roles
        $supplierRoles = ['Supplier', 'Supplier Admin'];
        
        foreach ($supplierRoles as $roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if ($role) {
                $role->delete();
                $this->info("Removed role: {$roleName}");
            }
        }

        // Remove supplier permissions
        $supplierPermissions = [
            'view_supplier_profile',
            'edit_supplier_profile',
            'view_supplier_services',
            'create_supplier_services',
            'edit_supplier_services',
            'delete_supplier_services',
            'view_supplier_bookings',
            'manage_supplier_bookings',
            'view_supplier_wallet',
            'manage_supplier_wallet',
            'view_supplier_statistics',
            'view_all_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',
            'approve_supplier_services',
            'reject_supplier_services',
            'manage_supplier_commissions',
            'view_supplier_analytics',
        ];

        foreach ($supplierPermissions as $permissionName) {
            $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
                $this->info("Removed permission: {$permissionName}");
            }
        }
    }

    /**
     * Clean up any remaining data.
     */
    private function cleanupData()
    {
        // Clean up any remaining supplier data
        $tables = [
            'supplier_transport_bookings',
            'supplier_tour_bookings',
            'supplier_hotel_bookings',
            'supplier_trip_bookings',
            'supplier_transports',
            'supplier_tours',
            'supplier_hotels',
            'supplier_trips',
            'suppliers',
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("Truncated table: {$table}");
            }
        }
    }
}
