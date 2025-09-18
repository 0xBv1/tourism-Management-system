<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\City;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        // Get a city to use for resources
        $city = City::first();
        if (!$city) {
            $city = City::create([
                'name' => 'Sample City',
            ]);
        }

        // Create sample hotels
        Hotel::create([
            'name' => 'Grand Hotel Plaza',
            'description' => 'Luxury hotel in the city center',
            'address' => '123 Main Street',
            'city_id' => $city->id,
            'phone' => '+1-555-0123',
            'email' => 'info@grandhotel.com',
            'website' => 'https://grandhotel.com',
            'star_rating' => 5,
            'total_rooms' => 100,
            'available_rooms' => 95,
            'price_per_night' => 250.00,
            'currency' => 'USD',
            'amenities' => json_encode(['wifi', 'pool', 'gym', 'restaurant']),
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        Hotel::create([
            'name' => 'Budget Inn',
            'description' => 'Affordable accommodation for travelers',
            'address' => '456 Oak Avenue',
            'city_id' => $city->id,
            'phone' => '+1-555-0456',
            'email' => 'info@budgetinn.com',
            'website' => 'https://budgetinn.com',
            'star_rating' => 3,
            'total_rooms' => 50,
            'available_rooms' => 48,
            'price_per_night' => 80.00,
            'currency' => 'USD',
            'amenities' => json_encode(['wifi', 'parking']),
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        // Create sample vehicles
        Vehicle::create([
            'name' => 'Luxury Bus',
            'type' => 'bus',
            'brand' => 'Mercedes',
            'model' => 'Tourismo',
            'year' => 2023,
            'license_plate' => 'BUS-001',
            'capacity' => 50,
            'description' => 'Comfortable 50-seat luxury bus',
            'city_id' => $city->id,
            'driver_name' => 'John Driver',
            'driver_phone' => '+1-555-0789',
            'driver_license' => 'DL123456',
            'price_per_hour' => 150.00,
            'price_per_day' => 1200.00,
            'currency' => 'USD',
            'fuel_type' => 'Diesel',
            'transmission' => 'Automatic',
            'features' => json_encode(['ac', 'wifi', 'tv']),
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        Vehicle::create([
            'name' => 'Executive Car',
            'type' => 'sedan',
            'brand' => 'BMW',
            'model' => '7 Series',
            'year' => 2023,
            'license_plate' => 'EXE-001',
            'capacity' => 4,
            'description' => 'Luxury sedan for executive transport',
            'city_id' => $city->id,
            'driver_name' => 'Mike Chauffeur',
            'driver_phone' => '+1-555-0321',
            'driver_license' => 'DL789012',
            'price_per_hour' => 80.00,
            'price_per_day' => 600.00,
            'currency' => 'USD',
            'fuel_type' => 'Gasoline',
            'transmission' => 'Automatic',
            'features' => json_encode(['ac', 'wifi', 'leather_seats']),
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        // Create sample guides
        Guide::create([
            'name' => 'John Smith',
            'description' => 'Experienced city tour guide',
            'address' => '654 Guide Lane',
            'city_id' => $city->id,
            'phone' => '+1-555-0654',
            'email' => 'john@guides.com',
            'languages' => json_encode(['English', 'Spanish']),
            'specialties' => json_encode(['city_tours', 'historical_sites']),
            'price_per_hour' => 50.00,
            'currency' => 'USD',
            'rating' => 4.8,
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        Guide::create([
            'name' => 'Maria Garcia',
            'description' => 'Nature and adventure specialist',
            'address' => '987 Adventure Road',
            'city_id' => $city->id,
            'phone' => '+1-555-0987',
            'email' => 'maria@guides.com',
            'languages' => json_encode(['English', 'Spanish', 'French']),
            'specialties' => json_encode(['nature_tours', 'adventure', 'hiking']),
            'price_per_hour' => 60.00,
            'currency' => 'USD',
            'rating' => 4.9,
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        // Create sample representatives
        Representative::create([
            'name' => 'Alice Johnson',
            'description' => 'Senior tourism representative',
            'address' => '147 Representative Street',
            'city_id' => $city->id,
            'phone' => '+1-555-0147',
            'email' => 'alice@representatives.com',
            'languages' => json_encode(['English', 'German']),
            'specialties' => json_encode(['group_tours', 'corporate_events']),
            'price_per_hour' => 75.00,
            'currency' => 'USD',
            'rating' => 4.7,
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);

        Representative::create([
            'name' => 'David Wilson',
            'description' => 'Cultural and heritage specialist',
            'address' => '258 Heritage Avenue',
            'city_id' => $city->id,
            'phone' => '+1-555-0258',
            'email' => 'david@representatives.com',
            'languages' => json_encode(['English', 'Italian']),
            'specialties' => json_encode(['cultural_tours', 'museums', 'art_galleries']),
            'price_per_hour' => 70.00,
            'currency' => 'USD',
            'rating' => 4.6,
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
        ]);
    }
}
