<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Duration;
use Illuminate\Support\Str;

class DurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $durations = [
            [
                'title' => '1 Day',
                'description' => 'Perfect for a quick day trip or experience',
                'days' => 1,
                'nights' => 0,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 1
            ],
            [
                'title' => '2 Days 1 Night',
                'description' => 'Great for a weekend getaway',
                'days' => 2,
                'nights' => 1,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => true,
                'display_order' => 2
            ],
            [
                'title' => '3 Days 2 Nights',
                'description' => 'Perfect for a short vacation',
                'days' => 3,
                'nights' => 2,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => true,
                'display_order' => 3
            ],
            [
                'title' => '4 Days 3 Nights',
                'description' => 'Extended weekend adventure',
                'days' => 4,
                'nights' => 3,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 4
            ],
            [
                'title' => '5 Days 4 Nights',
                'description' => 'Ideal for exploring multiple destinations',
                'days' => 5,
                'nights' => 4,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => true,
                'display_order' => 5
            ],
            [
                'title' => '6 Days 5 Nights',
                'description' => 'Almost a full week of exploration',
                'days' => 6,
                'nights' => 5,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 6
            ],
            [
                'title' => '7 Days 6 Nights',
                'description' => 'A full week of adventure',
                'days' => 7,
                'nights' => 6,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 7
            ],
            [
                'title' => '8 Days 7 Nights',
                'description' => 'Extended week tour',
                'days' => 8,
                'nights' => 7,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 8
            ],
            [
                'title' => '9 Days 8 Nights',
                'description' => 'Nine days of discovery',
                'days' => 9,
                'nights' => 8,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 9
            ],
            [
                'title' => '10 Days 9 Nights',
                'description' => 'Extended vacation package',
                'days' => 10,
                'nights' => 9,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 10
            ],
            [
                'title' => '12 Days 11 Nights',
                'description' => 'Nearly two weeks of adventure',
                'days' => 12,
                'nights' => 11,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 11
            ],
            [
                'title' => '14 Days 13 Nights',
                'description' => 'Two weeks of incredible experiences',
                'days' => 14,
                'nights' => 13,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 12
            ],
            [
                'title' => '15 Days 14 Nights',
                'description' => 'Extended two-week tour',
                'days' => 15,
                'nights' => 14,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 13
            ],
            [
                'title' => '21 Days 20 Nights',
                'description' => 'Three weeks of exploration',
                'days' => 21,
                'nights' => 20,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 14
            ],
            [
                'title' => '30 Days 29 Nights',
                'description' => 'Full month adventure',
                'days' => 30,
                'nights' => 29,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 15
            ],
            [
                'title' => '1 Hour',
                'description' => 'Quick activity or experience',
                'days' => 1,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 16
            ],
            [
                'title' => '2 Hours',
                'description' => 'Quick tour or activity',
                'days' => 2,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 17
            ],
            [
                'title' => '3 Hours',
                'description' => 'Short guided experience',
                'days' => 3,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 18
            ],
            [
                'title' => '4 Hours',
                'description' => 'Half-day experience',
                'days' => 4,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 19
            ],
            [
                'title' => '6 Hours',
                'description' => 'Extended half-day tour',
                'days' => 6,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 20
            ],
            [
                'title' => '8 Hours',
                'description' => 'Full-day experience',
                'days' => 8,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 21
            ],
            [
                'title' => '10 Hours',
                'description' => 'Extended full-day tour',
                'days' => 10,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 22
            ],
            [
                'title' => '12 Hours',
                'description' => 'All-day adventure',
                'days' => 12,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 23
            ]
        ];

        foreach ($durations as $durationData) {
            $duration = Duration::create([
                'slug' => Str::slug($durationData['title']),
                'enabled' => $durationData['enabled'],
                'featured' => $durationData['featured'],
                'display_order' => $durationData['display_order'],
                'days' => $durationData['days'],
                'nights' => $durationData['nights'],
                'duration_type' => $durationData['duration_type'],
            ]);

            // Add translations
            $duration->translateOrNew('en')->title = $durationData['title'];
            $duration->translateOrNew('en')->description = $durationData['description'];
            $duration->save();

            echo "Created duration: {$durationData['title']}\n";
        }
    }
}

