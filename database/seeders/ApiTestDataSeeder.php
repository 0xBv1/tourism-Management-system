<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duration;
use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding API test data...');

        // Create a test duration
        $duration = Duration::updateOrCreate(
            ['slug' => '1-day'],
            [
                'enabled' => true,
                'featured' => true,
                'display_order' => 1,
                'days' => 1,
                'nights' => 0,
                'duration_type' => 'days',
            ]
        );

        $duration->translateOrNew('en')->title = '1 Day';
        $duration->translateOrNew('en')->description = 'Perfect for a quick day trip or experience';
        $duration->save();

        // Create a test supplier user
        $user = User::updateOrCreate(
            ['email' => 'test@supplier.com'],
            [
                'name' => 'Test Supplier',
                'email' => 'test@supplier.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('Supplier');

        // Create a test supplier
        $supplier = Supplier::updateOrCreate(
            ['company_email' => 'test@supplier.com'],
            [
                'user_id' => $user->id,
                'company_name' => 'Test Travel Agency',
                'company_email' => 'test@supplier.com',
                'phone' => '+201234567890',
                'address' => '123 Test Street, Cairo, Egypt',
                'commission_rate' => 15.00,
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]
        );

        // Create a test supplier hotel
        SupplierHotel::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'name' => 'Test Hotel'
            ],
            [
                'supplier_id' => $supplier->id,
                'name' => 'Test Hotel',
                'description' => 'A test hotel for API testing',
                'address' => '123 Test Street, Cairo',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'phone' => '+201234567890',
                'email' => 'info@testhotel.com',
                'website' => 'https://testhotel.com',
                'stars' => 4,
                'price_per_night' => 200.00,
                'currency' => 'EGP',
                'amenities' => json_encode(['WiFi', 'Pool', 'Restaurant']),
                'enabled' => true,
                'approved' => true,
            ]
        );

        // Create a test supplier tour
        SupplierTour::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'title' => 'Test Tour'
            ],
            [
                'supplier_id' => $supplier->id,
                'title' => 'Test Tour',
                'description' => 'A test tour for API testing',
                'highlights' => 'Test highlights',
                'included' => 'Transport, Guide',
                'excluded' => 'Personal Expenses',
                'duration' => '4 Hours',
                'type' => 'Cultural',
                'pickup_location' => 'Cairo Hotels',
                'dropoff_location' => 'Cairo Hotels',
                'adult_price' => 100.00,
                'child_price' => 50.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 10,
                'enabled' => true,
                'approved' => true,
            ]
        );

        // Create a test supplier trip
        SupplierTrip::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'trip_name' => 'Test Trip'
            ],
            [
                'supplier_id' => $supplier->id,
                'trip_name' => 'Test Trip',
                'trip_type' => 'one_way',
                'departure_city' => 'Cairo',
                'arrival_city' => 'Alexandria',
                'travel_date' => Carbon::now()->addDays(7),
                'return_date' => null,
                'departure_time' => '08:00:00',
                'arrival_time' => '10:30:00',
                'seat_price' => 150.00,
                'total_seats' => 30,
                'available_seats' => 25,
                'additional_notes' => 'Test trip notes',
                'amenities' => json_encode(['WiFi', 'Air Conditioning']),
                'enabled' => true,
                'approved' => true,
            ]
        );

        // Create a test supplier transport
        SupplierTransport::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'name' => 'Test Transport'
            ],
            [
                'supplier_id' => $supplier->id,
                'name' => 'Test Transport',
                'description' => 'A test transport service for API testing',
                'origin_location' => 'Cairo Airport',
                'destination_location' => 'Cairo City Center',
                'estimated_travel_time' => 45,
                'distance' => 25.0,
                'route_type' => 'airport_transfer',
                'price' => 100.00,
                'currency' => 'EGP',
                'vehicle_type' => 'Sedan',
                'seating_capacity' => 4,
                'amenities' => json_encode(['WiFi', 'Air Conditioning']),
                'enabled' => true,
                'approved' => true,
            ]
        );

        $this->command->info('API test data seeded successfully!');
        $this->command->info('Test supplier email: test@supplier.com');
        $this->command->info('Test supplier password: password123');
    }
}

