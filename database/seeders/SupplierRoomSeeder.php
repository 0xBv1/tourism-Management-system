<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplierRoom;
use App\Models\SupplierHotel;
use App\Models\Amenity;

class SupplierRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierHotels = SupplierHotel::all();
        $amenities = Amenity::all();

        if ($supplierHotels->isEmpty()) {
            $this->command->warn('No supplier hotels found. Please run SupplierHotelSeeder first.');
            return;
        }

        $roomTypes = [
            'Standard Room',
            'Deluxe Room',
            'Suite',
            'Executive Room',
            'Family Room',
            'Presidential Suite'
        ];

        $bedTypes = [
            '1 King Bed',
            '2 Twin Beds',
            '1 Queen Bed',
            '1 King + 1 Sofa Bed',
            '2 Queen Beds',
            '1 King + 2 Twin Beds'
        ];

        $roomCounter = 1;
        foreach ($supplierHotels as $hotel) {
            // Create 3-6 rooms per hotel
            $roomCount = rand(3, 6);
            
            for ($i = 0; $i < $roomCount; $i++) {
                $room = SupplierRoom::create([
                    'slug' => 'room-' . $roomCounter,
                    'supplier_hotel_id' => $hotel->id,
                    'enabled' => rand(0, 1),
                    'bed_count' => rand(1, 3),
                    'room_type' => $roomTypes[array_rand($roomTypes)],
                    'max_capacity' => rand(2, 6),
                    'bed_types' => $bedTypes[array_rand($bedTypes)],
                    'night_price' => rand(50, 500),
                    'extra_bed_available' => rand(0, 1),
                    'extra_bed_price' => rand(0, 1) ? rand(20, 100) : null,
                    'max_extra_beds' => rand(0, 1) ? rand(1, 3) : 1,
                    'extra_bed_description' => rand(0, 1) ? 'Additional comfortable bed available upon request' : null,
                    'approved' => rand(0, 1),
                ]);

                // Add translations
                $room->translateOrNew('en')->fill([
                    'name' => $room->room_type . ' ' . ($i + 1),
                    'description' => 'Comfortable and well-appointed ' . strtolower($room->room_type) . ' with modern amenities and beautiful views.',
                ]);
                $room->save();

                // Attach random amenities
                $randomAmenities = $amenities->random(rand(3, 6));
                $room->amenities()->attach($randomAmenities->pluck('id')->toArray());

                // Create SEO data
                $room->seo()->create([
                    'en' => [
                        'meta_title' => $room->name . ' - ' . $hotel->name,
                        'meta_description' => 'Book ' . $room->name . ' at ' . $hotel->name . ' for a comfortable stay.',
                        'meta_keywords' => 'hotel room, accommodation, ' . strtolower($room->room_type) . ', ' . strtolower($hotel->name),
                    ]
                ]);
                
                $roomCounter++;
            }
        }

        $this->command->info('Supplier rooms seeded successfully!');
    }
}
