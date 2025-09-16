<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Amenity;
use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();
        $amenities = Amenity::all();

        $roomTypes = ['single', 'double', 'deluxe', 'suite', 'family'];
        $bedTypes = ['single', 'double', 'queen', 'king', 'twin'];

        foreach ($hotels as $hotel) {
            // Create 3-8 rooms per hotel
            $roomCount = rand(3, 8);
            
            for ($i = 1; $i <= $roomCount; $i++) {
                $roomType = $roomTypes[array_rand($roomTypes)];
                $bedType = $bedTypes[array_rand($bedTypes)];
                $bedCount = rand(1, 4);
                $maxCapacity = $bedCount * rand(1, 2);
                $nightPrice = rand(50, 500);

                $room = Room::create([
                    'hotel_id' => $hotel->id,
                    'slug' => Str::slug($hotel->name . '-room-' . $i),
                    'featured_image' => null,
                    'banner' => null,
                    'gallery' => [],
                    'enabled' => true,
                    'bed_count' => $bedCount,
                    'max_capacity' => $maxCapacity,
                    'room_type' => $roomType,
                    'bed_types' => $bedType,
                    'night_price' => $nightPrice,
                ]);

                // Attach random amenities to each room
                $randomAmenities = $amenities->random(rand(2, 6));
                $room->amenities()->attach($randomAmenities->pluck('id')->toArray());
            }
        }

        $this->command->info('Rooms seeded successfully!');
    }
}

