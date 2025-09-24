<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Extra;

class ExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $extras = [
            [
                'name' => 'Airport Transfer',
                'description' => 'Private transfer from/to airport',
                'price' => 50.00,
                'currency' => 'USD',
                'category' => 'transportation',
                'features' => ['Private vehicle', 'Professional driver', 'Meet & greet service'],
            ],
            [
                'name' => 'City Tour',
                'description' => 'Half-day guided city tour',
                'price' => 75.00,
                'currency' => 'USD',
                'category' => 'activities',
                'features' => ['Professional guide', 'Transportation', 'Entrance fees'],
            ],
            [
                'name' => 'Photography Service',
                'description' => 'Professional photography during tour',
                'price' => 100.00,
                'currency' => 'USD',
                'category' => 'services',
                'features' => ['Professional photographer', 'Digital photos', 'Same-day delivery'],
            ],
            [
                'name' => 'Dinner Reservation',
                'description' => 'Fine dining restaurant reservation',
                'price' => 25.00,
                'currency' => 'USD',
                'category' => 'services',
                'features' => ['Restaurant booking', 'Table confirmation', 'Special requests'],
            ],
            [
                'name' => 'Spa Package',
                'description' => 'Relaxing spa treatment package',
                'price' => 150.00,
                'currency' => 'USD',
                'category' => 'services',
                'features' => ['Massage therapy', 'Sauna access', 'Refreshments'],
            ],
        ];

        foreach ($extras as $extra) {
            Extra::create($extra);
        }
    }
}
