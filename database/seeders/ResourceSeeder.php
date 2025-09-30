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
            'email' => 'john@guides.com',
            'phone' => '+1-555-0654',
            'nationality' => 'American',
            'languages' => ['English', 'Spanish'],
            'specializations' => ['city_tours', 'historical_sites'],
            'experience_years' => 5,
            'city_id' => $city->id,
            'price_per_hour' => 50.00,
            'price_per_day' => 350.00,
            'currency' => 'USD',
            'bio' => 'Experienced city tour guide',
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
            'rating' => 4.8,
            'total_ratings' => 50,
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '+1-555-0655',
        ]);

        Guide::create([
            'name' => 'Maria Garcia',
            'email' => 'maria@guides.com',
            'phone' => '+1-555-0987',
            'nationality' => 'Spanish',
            'languages' => ['English', 'Spanish', 'French'],
            'specializations' => ['nature_tours', 'adventure', 'hiking'],
            'experience_years' => 8,
            'city_id' => $city->id,
            'price_per_hour' => 60.00,
            'price_per_day' => 420.00,
            'currency' => 'USD',
            'bio' => 'Nature and adventure specialist',
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
            'rating' => 4.9,
            'total_ratings' => 75,
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '+1-555-0988',
        ]);

        // Create sample representatives
        Representative::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@representatives.com',
            'phone' => '+1-555-0147',
            'nationality' => 'American',
            'languages' => ['English', 'German'],
            'specializations' => ['group_tours', 'corporate_events'],
            'experience_years' => 6,
            'city_id' => $city->id,
            'price_per_hour' => 75.00,
            'price_per_day' => 525.00,
            'currency' => 'USD',
            'bio' => 'Senior tourism representative',
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
            'rating' => 4.7,
            'total_ratings' => 60,
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '+1-555-0148',
            'company_name' => 'Tourism Solutions Inc',
            'company_license' => 'LIC-001',
            'service_areas' => ['Downtown', 'Airport', 'Hotel District'],
        ]);

        Representative::create([
            'name' => 'David Wilson',
            'email' => 'david@representatives.com',
            'phone' => '+1-555-0258',
            'nationality' => 'British',
            'languages' => ['English', 'Italian'],
            'specializations' => ['cultural_tours', 'museums', 'art_galleries'],
            'experience_years' => 7,
            'city_id' => $city->id,
            'price_per_hour' => 70.00,
            'price_per_day' => 490.00,
            'currency' => 'USD',
            'bio' => 'Cultural and heritage specialist',
            'status' => ResourceStatus::AVAILABLE,
            'active' => true,
            'enabled' => true,
            'rating' => 4.6,
            'total_ratings' => 45,
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '+1-555-0259',
            'company_name' => 'Cultural Services Ltd',
            'company_license' => 'LIC-002',
            'service_areas' => ['Museum District', 'Cultural Center', 'Historic Area'],
        ]);
    }
}
