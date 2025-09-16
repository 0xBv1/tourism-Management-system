<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\SupplierTour;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierTourFactory extends Factory
{
    protected $model = SupplierTour::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'highlights' => $this->faker->paragraph(),
            'included' => $this->faker->sentence(),
            'excluded' => $this->faker->sentence(),
            'duration' => $this->faker->randomElement(['1 Day', '2 Days', '3 Days', '1 Week']),
            'type' => $this->faker->randomElement(['Cultural', 'Adventure', 'Relaxation', 'Educational']),
            'pickup_location' => $this->faker->city(),
            'dropoff_location' => $this->faker->city(),
            'adult_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 100, max: 4000),
            'child_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 10, max: 99),
            'infant_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 1, max: 9),
            'max_group_size' => $this->faker->numberBetween(10, 50),
            'slug' => $this->faker->slug(),
            'code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'display_order' => $this->faker->numberBetween(1, 100),
            'featured' => $this->faker->boolean(),
            'duration_in_days' => $this->faker->numberBetween(1, 14),
            'pricing_groups' => [
                [
                    'from' => 1,
                    'to' => 2,
                    'price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 100, max: 4000),
                    'child_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 10, max: 99),
                ],
                [
                    'from' => 3,
                    'to' => 5,
                    'price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 90, max: 3800),
                    'child_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 9, max: 89),
                ],
                [
                    'from' => 6,
                    'to' => 10,
                    'price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 80, max: 3600),
                    'child_price' => $this->faker->randomFloat(nbMaxDecimals: 2, min: 8, max: 79),
                ],
            ],
            'enabled' => $this->faker->boolean(80), // 80% chance of being enabled
            'approved' => $this->faker->boolean(70), // 70% chance of being approved
        ];
    }
}



