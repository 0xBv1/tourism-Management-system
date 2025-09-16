<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierHotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierHotel>
 */
class SupplierHotelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'stars' => $this->faker->numberBetween(1, 5),
            'enabled' => $this->faker->boolean(80),
            'featured_image' => $this->faker->imageUrl(640, 480, 'hotel'),
            'banner' => $this->faker->imageUrl(1200, 400, 'hotel'),
            'gallery' => $this->faker->randomElements([
                $this->faker->imageUrl(800, 600, 'hotel'),
                $this->faker->imageUrl(800, 600, 'hotel'),
                $this->faker->imageUrl(800, 600, 'hotel'),
            ], $this->faker->numberBetween(1, 3)),
            'address' => $this->faker->address(),
            'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3454.123456789!2d31.123456789!3d30.123456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDA3JzM0LjQiTiAzMcKwMDcnMzQuNCJF!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
            'slug' => $this->faker->unique()->slug(),
            'phone_contact' => $this->faker->phoneNumber(),
            'whatsapp_contact' => $this->faker->phoneNumber(),
            'approved' => $this->faker->boolean(70),
            'rejection_reason' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the hotel is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved' => true,
        ]);
    }

    /**
     * Indicate that the hotel is enabled.
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => true,
        ]);
    }

    /**
     * Indicate that the hotel is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}



