<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--filename=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database before running migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        // Get database configuration
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        // Generate filename
        $filename = $this->option('filename') ?: 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('backups/' . $filename);

        // Create backups directory if it doesn't exist
        if (!file_exists(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        // Build mysqldump command
        $command = "mysqldump";
        $command .= " --host={$host}";
        $command .= " --port={$port}";
        $command .= " --user={$username}";
        
        if ($password) {
            $command .= " --password={$password}";
        }
        
        $command .= " --single-transaction";
        $command .= " --routines";
        $command .= " --triggers";
        $command .= " {$database}";
        $command .= " > {$backupPath}";

        // Execute backup command
        $this->info("Executing: {$command}");
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("Database backup completed successfully!");
            $this->info("Backup saved to: {$backupPath}");
            
            // Get file size
            $fileSize = filesize($backupPath);
            $this->info("Backup size: " . $this->formatBytes($fileSize));
            
            return 0;
        } else {
            $this->error("Database backup failed!");
            $this->error("Return code: {$returnCode}");
            $this->error("Output: " . implode("\n", $output));
            return 1;
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
