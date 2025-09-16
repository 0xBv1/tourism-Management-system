<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\SupplierHotelBooking;
use App\Models\SupplierTourBooking;
use App\Models\SupplierTripBooking;
use App\Models\SupplierTransportBooking;
use App\Models\SupplierWalletTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierStatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Empty seeder - no data to seed
        $this->command->info('SupplierStatisticsSeeder completed - no data seeded.');
    }
}

