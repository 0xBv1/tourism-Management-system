<?php

namespace Database\Factories;

use App\Models\SupplierRoom;
use App\Models\SupplierHotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierRoom>
 */
class SupplierRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

        return [
            'slug' => $this->faker->unique()->slug(),
            'supplier_hotel_id' => SupplierHotel::factory(),
            'enabled' => $this->faker->boolean(80),
            'bed_count' => $this->faker->numberBetween(1, 3),
            'room_type' => $this->faker->randomElement($roomTypes),
            'max_capacity' => $this->faker->numberBetween(2, 6),
            'bed_types' => $this->faker->randomElement($bedTypes),
            'night_price' => $this->faker->randomFloat(2, 50, 500),
            'extra_bed_available' => $this->faker->boolean(30),
            'extra_bed_price' => $this->faker->optional(0.3)->randomFloat(2, 20, 100),
            'max_extra_beds' => $this->faker->numberBetween(1, 3),
            'extra_bed_description' => $this->faker->optional(0.3)->sentence(),
            'approved' => $this->faker->boolean(70),
            'rejection_reason' => $this->faker->optional(0.1)->sentence(),
        ];
    }

    /**
     * Indicate that the room is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved' => true,
        ]);
    }

    /**
     * Indicate that the room is enabled.
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => true,
        ]);
    }

    /**
     * Indicate that the room has extra bed available.
     */
    public function withExtraBed(): static
    {
        return $this->state(fn (array $attributes) => [
            'extra_bed_available' => true,
            'extra_bed_price' => $this->faker->randomFloat(2, 20, 100),
            'max_extra_beds' => $this->faker->numberBetween(1, 3),
            'extra_bed_description' => $this->faker->sentence(),
        ]);
    }
}
