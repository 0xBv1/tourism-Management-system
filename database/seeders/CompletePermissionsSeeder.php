<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompletePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NewRolesSeeder::class,
            \Database\Seeders\Permissions\InquiryPermissionSeeder::class,
            \Database\Seeders\Permissions\RequestPermissionSeeder::class,
            \Database\Seeders\Permissions\BookingFilePermissionSeeder::class,
            \Database\Seeders\Permissions\FinancePermissionSeeder::class,
            \Database\Seeders\Permissions\MasterDataPermissionSeeder::class,
            \Database\Seeders\Permissions\AdvancedReportsPermissionSeeder::class,
            \Database\Seeders\Permissions\WorkflowPermissionSeeder::class,
            // Keep existing permission seeders
            \Database\Seeders\Permissions\BookingPermissionSeeder::class,
            \Database\Seeders\Permissions\PaymentPermissionSeeder::class,
            \Database\Seeders\Permissions\HotelPermissionSeeder::class,
            \Database\Seeders\Permissions\ReportsPermissionSeeder::class,
            \Database\Seeders\Permissions\ResourceAssignmentPermissionSeeder::class,
            // Create default users
            DefaultUsersSeeder::class,
        ]);
    }
}
