<?php

namespace Database\Seeders;

use App\Models\SupplierHotel;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierHotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        foreach ($suppliers as $supplier) {
            SupplierHotel::factory()
                ->count(rand(2, 5))
                ->for($supplier)
                ->create();
        }

        $this->command->info('Supplier hotels seeded successfully!');
    }
}

