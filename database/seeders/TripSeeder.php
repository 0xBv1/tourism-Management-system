<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\City;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $cities = City::all();

        $amenities = [
            ['Wi-Fi', 'AC', 'Water'],
            ['Wi-Fi', 'Snacks', 'AC', 'TV'],
            ['Wi-Fi', 'Snacks', 'AC', 'USB_Charging', 'Water'],
            ['Wi-Fi', 'Snacks', 'AC', 'TV', 'USB_Charging', 'Water', 'Blanket', 'Pillow'],
            ['Wi-Fi', 'AC', 'Water', 'Blanket'],
            ['Wi-Fi', 'Snacks', 'AC', 'TV', 'USB_Charging'],
            ['AC', 'Water', 'Blanket', 'Pillow'],
            ['Wi-Fi', 'Snacks', 'AC'],
        ];

        $tripTypes = ['one_way', 'round_trip', 'special_discount'];
        $tripTypeWeights = [60, 25, 15]; // 60% one-way, 25% round-trip, 15% special discount

        // Create one-way and special discount trips
        for ($i = 0; $i < 25; $i++) {
            $departureCity = $cities->random();
            $arrivalCity = $cities->random();
            
            // Make sure departure and arrival are different
            while ($departureCity->id === $arrivalCity->id) {
                $arrivalCity = $cities->random();
            }

            // Weighted random selection for trip type
            $tripType = $this->getWeightedRandom($tripTypes, $tripTypeWeights);
            
            $travelDate = Carbon::now()->addDays(rand(1, 60));
            $departureTime = Carbon::createFromTime(rand(6, 22), rand(0, 59));
            $arrivalTime = $departureTime->copy()->addHours(rand(2, 8));

            // Calculate realistic pricing based on distance and trip type
            $basePrice = $this->calculateBasePrice($departureCity->name, $arrivalCity->name);
            $finalPrice = $this->applyTripTypePricing($basePrice, $tripType);

            Trip::create([
                'trip_type' => $tripType,
                'departure_city_id' => $departureCity->id,
                'arrival_city_id' => $arrivalCity->id,
                'travel_date' => $travelDate,
                'return_date' => null,
                'seat_price' => $finalPrice,
                'total_seats' => rand(20, 50),
                'available_seats' => rand(5, 50),
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'additional_notes' => $this->generateAdditionalNotes($tripType),
                'amenities' => $amenities[array_rand($amenities)],
                'enabled' => rand(0, 10) > 1, // 90% chance of being enabled
            ]);
        }

        // Create round trips
        for ($i = 0; $i < 15; $i++) {
            $departureCity = $cities->random();
            $arrivalCity = $cities->random();
            
            // Make sure departure and arrival are different
            while ($departureCity->id === $arrivalCity->id) {
                $arrivalCity = $cities->random();
            }

            $travelDate = Carbon::now()->addDays(rand(1, 60));
            $returnDate = $travelDate->copy()->addDays(rand(1, 14));
            $departureTime = Carbon::createFromTime(rand(6, 22), rand(0, 59));
            $arrivalTime = $departureTime->copy()->addHours(rand(2, 8));

            // Calculate realistic pricing for round trips
            $basePrice = $this->calculateBasePrice($departureCity->name, $arrivalCity->name);
            $finalPrice = $this->applyTripTypePricing($basePrice, 'round_trip');

            Trip::create([
                'trip_type' => 'round_trip',
                'departure_city_id' => $departureCity->id,
                'arrival_city_id' => $arrivalCity->id,
                'travel_date' => $travelDate,
                'return_date' => $returnDate,
                'seat_price' => $finalPrice,
                'total_seats' => rand(20, 50),
                'available_seats' => rand(5, 50),
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'additional_notes' => $this->generateAdditionalNotes('round_trip'),
                'amenities' => $amenities[array_rand($amenities)],
                'enabled' => rand(0, 10) > 1, // 90% chance of being enabled
            ]);
        }

        echo "Sample trips created successfully!\n";
    }

    private function getWeightedRandom(array $items, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $item;
            }
        }
        
        return $items[0]; // fallback
    }

    private function calculateBasePrice(string $from, string $to): float
    {
        // Simulate distance-based pricing
        $cityDistances = [
            'Cairo' => ['Alexandria' => 220, 'Hurghada' => 450, 'Sharm El Sheikh' => 500, 'Luxor' => 650, 'Aswan' => 850],
            'Alexandria' => ['Cairo' => 220, 'Hurghada' => 600, 'Sharm El Sheikh' => 650, 'Luxor' => 800, 'Aswan' => 1000],
            'Hurghada' => ['Cairo' => 450, 'Alexandria' => 600, 'Sharm El Sheikh' => 150, 'Luxor' => 300, 'Aswan' => 500],
            'Sharm El Sheikh' => ['Cairo' => 500, 'Alexandria' => 650, 'Hurghada' => 150, 'Luxor' => 400, 'Aswan' => 600],
            'Luxor' => ['Cairo' => 650, 'Alexandria' => 800, 'Hurghada' => 300, 'Sharm El Sheikh' => 400, 'Aswan' => 200],
            'Aswan' => ['Cairo' => 850, 'Alexandria' => 1000, 'Hurghada' => 500, 'Sharm El Sheikh' => 600, 'Luxor' => 200],
        ];

        // Check if we have distance data for this route
        if (isset($cityDistances[$from][$to])) {
            $distance = $cityDistances[$from][$to];
        } elseif (isset($cityDistances[$to][$from])) {
            $distance = $cityDistances[$to][$from];
        } else {
            // Default distance calculation for other cities
            $distance = rand(100, 800);
        }

        // Base price calculation: 0.5 EGP per km + base fare
        $basePrice = ($distance * 0.5) + 50;
        
        return round($basePrice, 2);
    }

    private function applyTripTypePricing(float $basePrice, string $tripType): float
    {
        switch ($tripType) {
            case 'round_trip':
                return round($basePrice * 1.8, 2); // Round trip is 180% of one-way
            case 'special_discount':
                return round($basePrice * 0.7, 2); // Special discount is 70% of regular price
            default:
                return $basePrice;
        }
    }

    private function generateAdditionalNotes(string $tripType): ?string
    {
        $notes = [
            'Comfortable journey with modern amenities',
            'Professional driver and clean vehicle',
            'On-time departure guaranteed',
            'Free cancellation up to 24 hours before departure',
            'Child seats available upon request',
            'Wheelchair accessible vehicle available',
            'Pet-friendly transportation (small pets only)',
            'Luggage space available for all passengers',
            'Refreshments provided during the journey',
            'Wi-Fi available throughout the trip',
        ];

        if ($tripType === 'round_trip') {
            $roundTripNotes = [
                'Flexible return date options',
                'Same day return available for 6 days',
                'Return journey can be rescheduled',
                'Round trip discount applied',
            ];
            $notes = array_merge($notes, $roundTripNotes);
        }

        if ($tripType === 'special_discount') {
            $discountNotes = [
                'Limited time offer',
                'Special discount applied',
                'Early booking discount',
                'Group booking discount',
            ];
            $notes = array_merge($notes, $discountNotes);
        }

        return $notes[array_rand($notes)];
    }
}
