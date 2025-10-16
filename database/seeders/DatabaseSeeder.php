<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Core system seeders
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CitySeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(PermissionsSeeder::class);
        
        // User and resource seeders
        $this->call(DefaultUsersSeeder::class);
        $this->call(ResourceSeeder::class);
        
        // Tourism resource seeders
        $this->call(HotelSeeder::class);
        $this->call(VehicleSeeder::class);
        $this->call(GuideSeeder::class);
        $this->call(RepresentativeSeeder::class);
        
        // Client seeders
        $this->call(ClientSeeder::class);
        
        // Business process seeders
        $this->call(InquirySeeder::class);
        $this->call(BookingFileSeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(ResourceBookingSeeder::class);
        $this->call(InquiryResourceSeeder::class);
        
        // Additional seeders
        $this->call(ChatSeeder::class);
    }
}