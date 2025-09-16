<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transport;
use App\Models\Amenity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = Amenity::all();

        $transports = [
            [
                'name' => 'Luxury Airport Transfer Service',
                'description' => 'Premium airport transfer service with professional drivers and luxury vehicles. Perfect for business travelers and tourists seeking comfort and reliability.',
                'transport_type' => 'car',
                'vehicle_type' => 'sedan',
                'seating_capacity' => 4,
                'origin_location' => 'Cairo International Airport',
                'destination_location' => 'Cairo City Center',
                'intermediate_stops' => ['Heliopolis', 'Nasr City'],
                'estimated_travel_time' => 45,
                'distance' => 25.5,
                'route_type' => 'with_stops',
                'price' => 150.00,
                'currency' => 'EGP',
                'vehicle_registration' => 'CAI-12345',
                'enabled' => true,
                'slug' => 'luxury-airport-transfer-service',
                'phone_contact' => '+20-2-1234-5678',
                'whatsapp_contact' => '+20-10-1234-5678',
                'email_contact' => 'airport@transport.com',
                'departure_time' => '06:00',
                'arrival_time' => '06:45',
                'departure_location' => 'Airport Terminal 1',
                'arrival_location' => 'Downtown Cairo',
                'price_per_hour' => 200.00,
                'price_per_day' => 1200.00,
                'price_per_km' => 6.00,
                'discount_percentage' => 10.00,
                'discount_conditions' => 'Book 24 hours in advance',
            ],
            [
                'name' => 'Luxor Temple Shuttle Bus',
                'description' => 'Convenient shuttle service between Luxor hotels and major temple complexes. Includes professional guide and air-conditioned comfort.',
                'transport_type' => 'bus',
                'vehicle_type' => 'bus',
                'seating_capacity' => 25,
                'origin_location' => 'Luxor Hotels',
                'destination_location' => 'Karnak Temple Complex',
                'intermediate_stops' => ['Luxor Temple', 'Valley of the Kings'],
                'estimated_travel_time' => 20,
                'distance' => 8.0,
                'route_type' => 'with_stops',
                'price' => 80.00,
                'currency' => 'EGP',
                'vehicle_registration' => 'LUX-67890',
                'enabled' => true,
                'slug' => 'luxor-temple-shuttle-bus',
                'phone_contact' => '+20-95-2345-6789',
                'whatsapp_contact' => '+20-10-2345-6789',
                'email_contact' => 'luxor@transport.com',
                'departure_time' => '08:00',
                'arrival_time' => '08:20',
                'departure_location' => 'Hotel Pickup',
                'arrival_location' => 'Karnak Temple',
                'price_per_hour' => 120.00,
                'price_per_day' => 800.00,
                'price_per_km' => 10.00,
                'discount_percentage' => 15.00,
                'discount_conditions' => 'Group bookings (10+ people)',
            ],
            [
                'name' => 'Hurghada Beach Transfer',
                'description' => 'Reliable transfer service to Red Sea beaches and islands. Includes snorkeling equipment and refreshments.',
                'transport_type' => 'boat',
                'vehicle_type' => 'boat',
                'seating_capacity' => 12,
                'origin_location' => 'Hurghada Marina',
                'destination_location' => 'Giftun Island',
                'intermediate_stops' => ['El Gouna', 'Sahl Hasheesh'],
                'estimated_travel_time' => 30,
                'distance' => 15.0,
                'route_type' => 'direct',
                'price' => 120.00,
                'currency' => 'EGP',
                'vehicle_registration' => 'HRG-11111',
                'enabled' => true,
                'slug' => 'hurghada-beach-transfer',
                'phone_contact' => '+20-65-3456-7890',
                'whatsapp_contact' => '+20-10-3456-7890',
                'email_contact' => 'hurghada@transport.com',
                'departure_time' => '09:00',
                'arrival_time' => '09:30',
                'departure_location' => 'Hurghada Marina',
                'arrival_location' => 'Giftun Island Beach',
                'price_per_hour' => 150.00,
                'price_per_day' => 1000.00,
                'price_per_km' => 8.00,
                'discount_percentage' => 20.00,
                'discount_conditions' => 'Weekday bookings',
            ],
            [
                'name' => 'Alexandria City Tour Transport',
                'description' => 'Comprehensive city tour with professional guide covering all major attractions in Alexandria.',
                'transport_type' => 'bus',
                'vehicle_type' => 'bus',
                'seating_capacity' => 30,
                'origin_location' => 'Alexandria Hotels',
                'destination_location' => 'Various City Attractions',
                'intermediate_stops' => ['Bibliotheca Alexandrina', 'Qaitbay Citadel', 'Montazah Palace'],
                'estimated_travel_time' => 240,
                'distance' => 35.0,
                'route_type' => 'circular',
                'price' => 200.00,
                'currency' => 'EGP',
                'vehicle_registration' => 'ALX-22222',
                'enabled' => true,
                'slug' => 'alexandria-city-tour-transport',
                'phone_contact' => '+20-3-4567-8901',
                'whatsapp_contact' => '+20-10-4567-8901',
                'email_contact' => 'alexandria@transport.com',
                'departure_time' => '10:00',
                'arrival_time' => '14:00',
                'departure_location' => 'Hotel Pickup',
                'arrival_location' => 'Hotel Drop-off',
                'price_per_hour' => 50.00,
                'price_per_day' => 400.00,
                'price_per_km' => 5.70,
                'discount_percentage' => 25.00,
                'discount_conditions' => 'Students with ID',
            ],
            [
                'name' => 'Sinai Desert Safari Transport',
                'description' => 'Adventure transport for desert exploration and safari experiences in the Sinai Peninsula.',
                'transport_type' => 'car',
                'vehicle_type' => 'suv',
                'seating_capacity' => 6,
                'origin_location' => 'Sinai Hotels',
                'destination_location' => 'Desert Safari Locations',
                'intermediate_stops' => ['St. Catherine Monastery', 'Colored Canyon'],
                'estimated_travel_time' => 180,
                'distance' => 50.0,
                'route_type' => 'with_stops',
                'price' => 300.00,
                'currency' => 'EGP',
                'vehicle_registration' => 'SIN-33333',
                'enabled' => true,
                'slug' => 'sinai-desert-safari-transport',
                'phone_contact' => '+20-69-5678-9012',
                'whatsapp_contact' => '+20-10-5678-9012',
                'email_contact' => 'sinai@transport.com',
                'departure_time' => '07:00',
                'arrival_time' => '10:00',
                'departure_location' => 'Hotel Pickup',
                'arrival_location' => 'Desert Camp',
                'price_per_hour' => 100.00,
                'price_per_day' => 800.00,
                'price_per_km' => 6.00,
                'discount_percentage' => 30.00,
                'discount_conditions' => 'Multi-day bookings',
            ]
        ];

        foreach ($transports as $transportData) {
            // Check if transport already exists
            $existingTransport = Transport::where('slug', $transportData['slug'])->first();
            
            if ($existingTransport) {
                // Transport already exists, skip creation
                continue;
            }
            
            // Create transport
            $transport = Transport::create([
                'transport_type' => $transportData['transport_type'],
                'vehicle_type' => $transportData['vehicle_type'],
                'seating_capacity' => $transportData['seating_capacity'],
                'origin_location' => $transportData['origin_location'],
                'destination_location' => $transportData['destination_location'],
                'intermediate_stops' => $transportData['intermediate_stops'],
                'estimated_travel_time' => $transportData['estimated_travel_time'],
                'distance' => $transportData['distance'],
                'route_type' => $transportData['route_type'],
                'price' => $transportData['price'],
                'currency' => $transportData['currency'],
                'vehicle_registration' => $transportData['vehicle_registration'],
                'enabled' => $transportData['enabled'],
                'slug' => $transportData['slug'],
                'phone_contact' => $transportData['phone_contact'],
                'whatsapp_contact' => $transportData['whatsapp_contact'],
                'email_contact' => $transportData['email_contact'],
                'departure_time' => $transportData['departure_time'],
                'arrival_time' => $transportData['arrival_time'],
                'departure_location' => $transportData['departure_location'],
                'arrival_location' => $transportData['arrival_location'],
                'price_per_hour' => $transportData['price_per_hour'],
                'price_per_day' => $transportData['price_per_day'],
                'price_per_km' => $transportData['price_per_km'],
                'discount_percentage' => $transportData['discount_percentage'],
                'discount_conditions' => $transportData['discount_conditions'],
            ]);

            // Create transport translation
            DB::table('transport_translations')->insert([
                'transport_id' => $transport->id,
                'locale' => 'en',
                'name' => $transportData['name'],
                'description' => $transportData['description'],
            ]);
            
            // Attach random amenities to each transport
            $randomAmenities = $amenities->random(rand(3, 8));
            $transport->amenities()->attach($randomAmenities->pluck('id')->toArray());
        }

        $this->command->info('Transports seeded successfully!');
    }
}
