<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\City;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SystemTripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get cities for reference
        $cairo = City::where('name', 'Cairo')->first();
        $alexandria = City::where('name', 'Alexandria')->first();
        $luxor = City::where('name', 'Luxor')->first();
        $aswan = City::where('name', 'Aswan')->first();
        $hurghada = City::where('name', 'Hurghada')->first();
        $sharmElSheikh = City::where('name', 'Sharm El Sheikh')->first();
        $dahab = City::where('name', 'Dahab')->first();
        $marsaAlam = City::where('name', 'Marsa Alam')->first();
        $elGouna = City::where('name', 'El Gouna')->first();
        $portSaid = City::where('name', 'Port Said')->first();
        $siwa = City::where('name', 'Siwa')->first();
        $fayoum = City::where('name', 'Fayoum')->first();

        $systemTrips = [
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $alexandria->id ?? 2,
                'travel_date' => Carbon::now()->addDays(7),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(7)->setTime(8, 0),
                'arrival_time' => Carbon::now()->addDays(7)->setTime(10, 30),
                'seat_price' => 180.00,
                'total_seats' => 45,
                'available_seats' => 40,
                'additional_notes' => 'Premium service with WiFi and refreshments',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Alexandria Express',
            ],
            [
                'trip_type' => 'round_trip',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $luxor->id ?? 3,
                'travel_date' => Carbon::now()->addDays(10),
                'return_date' => Carbon::now()->addDays(15),
                'departure_time' => Carbon::now()->addDays(10)->setTime(7, 0),
                'arrival_time' => Carbon::now()->addDays(10)->setTime(12, 0),
                'seat_price' => 320.00,
                'total_seats' => 35,
                'available_seats' => 30,
                'additional_notes' => 'Luxury coach with entertainment system',
                'amenities' => ['WiFi', 'Air Conditioning', 'Entertainment', 'Refreshments', 'Reclining Seats'],
                'enabled' => true,
                'trip_name' => 'Cairo to Luxor Heritage Tour',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $aswan->id ?? 4,
                'travel_date' => Carbon::now()->addDays(5),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(5)->setTime(6, 30),
                'arrival_time' => Carbon::now()->addDays(5)->setTime(14, 0),
                'seat_price' => 380.00,
                'total_seats' => 30,
                'available_seats' => 25,
                'additional_notes' => 'Premium service with meal included',
                'amenities' => ['WiFi', 'Air Conditioning', 'Meal Included', 'Restroom', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Aswan Nile Journey',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $hurghada->id ?? 5,
                'travel_date' => Carbon::now()->addDays(3),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(3)->setTime(9, 0),
                'arrival_time' => Carbon::now()->addDays(3)->setTime(15, 0),
                'seat_price' => 220.00,
                'total_seats' => 40,
                'available_seats' => 35,
                'additional_notes' => 'Direct route to Red Sea resort',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Hurghada Beach Express',
            ],
            [
                'trip_type' => 'round_trip',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $sharmElSheikh->id ?? 6,
                'travel_date' => Carbon::now()->addDays(12),
                'return_date' => Carbon::now()->addDays(19),
                'departure_time' => Carbon::now()->addDays(12)->setTime(8, 0),
                'arrival_time' => Carbon::now()->addDays(12)->setTime(16, 0),
                'seat_price' => 280.00,
                'total_seats' => 30,
                'available_seats' => 28,
                'additional_notes' => 'Sinai adventure tour',
                'amenities' => ['WiFi', 'Air Conditioning', 'Entertainment', 'Refreshments', 'Reclining Seats'],
                'enabled' => true,
                'trip_name' => 'Cairo to Sharm El Sheikh Adventure',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $luxor->id ?? 3,
                'arrival_city_id' => $aswan->id ?? 4,
                'travel_date' => Carbon::now()->addDays(8),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(8)->setTime(9, 0),
                'arrival_time' => Carbon::now()->addDays(8)->setTime(12, 0),
                'seat_price' => 150.00,
                'total_seats' => 25,
                'available_seats' => 20,
                'additional_notes' => 'Nile Valley connection',
                'amenities' => ['WiFi', 'Air Conditioning', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Luxor to Aswan Nile Valley',
            ],
            [
                'trip_type' => 'round_trip',
                'departure_city_id' => $hurghada->id ?? 5,
                'arrival_city_id' => $dahab->id ?? 7,
                'travel_date' => Carbon::now()->addDays(14),
                'return_date' => Carbon::now()->addDays(17),
                'departure_time' => Carbon::now()->addDays(14)->setTime(10, 0),
                'arrival_time' => Carbon::now()->addDays(14)->setTime(12, 30),
                'seat_price' => 120.00,
                'total_seats' => 20,
                'available_seats' => 18,
                'additional_notes' => 'Red Sea coast tour',
                'amenities' => ['WiFi', 'Air Conditioning', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Hurghada to Dahab Coast Tour',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $marsaAlam->id ?? 8,
                'travel_date' => Carbon::now()->addDays(20),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(20)->setTime(7, 30),
                'arrival_time' => Carbon::now()->addDays(20)->setTime(14, 0),
                'seat_price' => 260.00,
                'total_seats' => 25,
                'available_seats' => 22,
                'additional_notes' => 'Remote Red Sea destination',
                'amenities' => ['WiFi', 'Air Conditioning', 'Meal Included', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Marsa Alam Remote',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $elGouna->id ?? 9,
                'travel_date' => Carbon::now()->addDays(16),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(16)->setTime(8, 0),
                'arrival_time' => Carbon::now()->addDays(16)->setTime(13, 0),
                'seat_price' => 200.00,
                'total_seats' => 30,
                'available_seats' => 27,
                'additional_notes' => 'Luxury resort destination',
                'amenities' => ['WiFi', 'Air Conditioning', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to El Gouna Luxury',
            ],
            [
                'trip_type' => 'round_trip',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $portSaid->id ?? 10,
                'travel_date' => Carbon::now()->addDays(18),
                'return_date' => Carbon::now()->addDays(20),
                'departure_time' => Carbon::now()->addDays(18)->setTime(9, 0),
                'arrival_time' => Carbon::now()->addDays(18)->setTime(11, 30),
                'seat_price' => 180.00,
                'total_seats' => 40,
                'available_seats' => 37,
                'additional_notes' => 'Weekend trip to Suez Canal',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Port Said Canal Tour',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $siwa->id ?? 11,
                'travel_date' => Carbon::now()->addDays(25),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(25)->setTime(6, 0),
                'arrival_time' => Carbon::now()->addDays(25)->setTime(18, 0),
                'seat_price' => 480.00,
                'total_seats' => 20,
                'available_seats' => 15,
                'additional_notes' => 'Adventure trip to magical oasis',
                'amenities' => ['WiFi', 'Air Conditioning', 'Meal Included', 'Entertainment', 'Refreshments', 'Reclining Seats'],
                'enabled' => true,
                'trip_name' => 'Cairo to Siwa Oasis Adventure',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $cairo->id ?? 1,
                'arrival_city_id' => $fayoum->id ?? 12,
                'travel_date' => Carbon::now()->addDays(13),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(13)->setTime(8, 30),
                'arrival_time' => Carbon::now()->addDays(13)->setTime(10, 30),
                'seat_price' => 110.00,
                'total_seats' => 30,
                'available_seats' => 25,
                'additional_notes' => 'Day trip to natural oasis',
                'amenities' => ['WiFi', 'Air Conditioning', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Cairo to Fayoum Nature Trip',
            ],
            [
                'trip_type' => 'round_trip',
                'departure_city_id' => $alexandria->id ?? 2,
                'arrival_city_id' => $luxor->id ?? 3,
                'travel_date' => Carbon::now()->addDays(22),
                'return_date' => Carbon::now()->addDays(29),
                'departure_time' => Carbon::now()->addDays(22)->setTime(7, 0),
                'arrival_time' => Carbon::now()->addDays(22)->setTime(16, 0),
                'seat_price' => 420.00,
                'total_seats' => 25,
                'available_seats' => 22,
                'additional_notes' => 'Cultural heritage tour',
                'amenities' => ['WiFi', 'Air Conditioning', 'Meal Included', 'Entertainment', 'Refreshments', 'Reclining Seats'],
                'enabled' => true,
                'trip_name' => 'Alexandria to Luxor Heritage',
            ],
            [
                'trip_type' => 'one_way',
                'departure_city_id' => $hurghada->id ?? 5,
                'arrival_city_id' => $sharmElSheikh->id ?? 6,
                'travel_date' => Carbon::now()->addDays(15),
                'return_date' => null,
                'departure_time' => Carbon::now()->addDays(15)->setTime(9, 0),
                'arrival_time' => Carbon::now()->addDays(15)->setTime(12, 0),
                'seat_price' => 160.00,
                'total_seats' => 35,
                'available_seats' => 30,
                'additional_notes' => 'Red Sea coast connection',
                'amenities' => ['WiFi', 'Air Conditioning', 'Refreshments'],
                'enabled' => true,
                'trip_name' => 'Hurghada to Sharm El Sheikh Coast',
            ],
        ];

        foreach ($systemTrips as $tripData) {
            // Check if trip already exists with same route and date
            $existingTrip = Trip::where('departure_city_id', $tripData['departure_city_id'])
                ->where('arrival_city_id', $tripData['arrival_city_id'])
                ->where('travel_date', $tripData['travel_date'])
                ->where('departure_time', $tripData['departure_time'])
                ->first();
            
            if ($existingTrip) {
                // Trip already exists, skip creation
                continue;
            }
            
            Trip::create($tripData);
        }

        $this->command->info('System Trips seeded successfully!');
    }
}

