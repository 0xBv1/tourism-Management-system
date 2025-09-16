<?php

namespace Database\Seeders;

use App\Models\SupplierHotel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateSupplierHotelTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting migration of supplier hotel translations...');

        // Get all supplier hotels that don't have translations yet
        $supplierHotels = SupplierHotel::whereDoesntHave('translations')->get();

        if ($supplierHotels->isEmpty()) {
            $this->command->info('No supplier hotels found without translations.');
            return;
        }

        $this->command->info("Found {$supplierHotels->count()} supplier hotels to migrate.");

        foreach ($supplierHotels as $hotel) {
            // Create translation records for each supported locale
            foreach (config('translatable.locales') as $locale) {
                DB::table('supplier_hotel_translations')->insert([
                    'supplier_hotel_id' => $hotel->id,
                    'locale' => $locale,
                    'name' => $hotel->getRawOriginal('name') ?? 'Hotel ' . $hotel->id,
                    'description' => $hotel->getRawOriginal('description'),
                    'city' => $hotel->getRawOriginal('city') ?? 'Unknown City',
                ]);
            }

            // Generate slug if not exists
            if (empty($hotel->slug)) {
                $hotel->update([
                    'slug' => SupplierHotel::generateSlug($hotel->name ?? 'Hotel ' . $hotel->id, $hotel->id)
                ]);
            }
        }

        $this->command->info('Supplier hotel translations migration completed successfully!');
    }
}

