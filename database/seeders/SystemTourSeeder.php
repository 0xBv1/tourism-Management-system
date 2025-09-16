<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Duration;
use Illuminate\Database\Seeder;

class SystemTourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemTours = [
            [
                'title' => 'Cairo Pyramids & Sphinx Day Tour',
                'overview' => 'Explore the ancient wonders of Egypt with our comprehensive Cairo Pyramids tour. Visit the Great Pyramid of Giza, Sphinx, and learn about ancient Egyptian civilization.',
                'slug' => 'cairo-pyramids-sphinx-day-tour',
                'duration_id' => 1,
                'adult_price' => 180.00,
                'child_price' => 120.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 1,
                'code' => 'SYSTOUR001',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Luxor Valley of Kings & Queens',
                'overview' => 'Discover the tombs of ancient pharaohs in the Valley of the Kings and Queens. Explore the magnificent temples and learn about the New Kingdom period.',
                'slug' => 'luxor-valley-kings-queens',
                'duration_id' => 3,
                'adult_price' => 350.00,
                'child_price' => 250.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 2,
                'code' => 'SYSTOUR002',
                'duration_in_days' => 2,
            ],
            [
                'title' => 'Aswan Nile Cruise & Temples',
                'overview' => 'Cruise along the majestic Nile River and visit historic temples. Experience the beauty of the Nile Valley and ancient Egyptian monuments.',
                'slug' => 'aswan-nile-cruise-temples',
                'duration_id' => 4,
                'adult_price' => 520.00,
                'child_price' => 420.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 3,
                'code' => 'SYSTOUR003',
                'duration_in_days' => 3,
            ],
            [
                'title' => 'Egypt Complete Heritage Experience',
                'overview' => 'Comprehensive tour covering all major historical sites in Egypt. From Cairo to Luxor to Aswan, experience the full Egyptian heritage.',
                'slug' => 'egypt-complete-heritage-experience',
                'duration_id' => 5,
                'adult_price' => 850.00,
                'child_price' => 700.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 4,
                'code' => 'SYSTOUR004',
                'duration_in_days' => 5,
            ],
            [
                'title' => 'Alexandria Mediterranean Discovery',
                'overview' => 'Explore the Mediterranean coast and ancient Alexandria. Visit the Library of Alexandria, Pompey\'s Pillar, and the Catacombs.',
                'slug' => 'alexandria-mediterranean-discovery',
                'duration_id' => 6,
                'adult_price' => 980.00,
                'child_price' => 800.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 5,
                'code' => 'SYSTOUR005',
                'duration_in_days' => 7,
            ],
            [
                'title' => 'Sinai Desert & Mount Sinai Adventure',
                'overview' => 'Adventure through the stunning Sinai desert landscape. Experience Bedouin culture and visit Mount Sinai and St. Catherine\'s Monastery.',
                'slug' => 'sinai-desert-mount-sinai-adventure',
                'duration_id' => 7,
                'adult_price' => 1350.00,
                'child_price' => 1100.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 6,
                'code' => 'SYSTOUR006',
                'duration_in_days' => 10,
            ],
            [
                'title' => 'Red Sea Diving & Marine Life',
                'overview' => 'Discover the underwater wonders of the Red Sea. Professional diving instruction and equipment provided.',
                'slug' => 'red-sea-diving-marine-life',
                'duration_id' => 2,
                'adult_price' => 280.00,
                'child_price' => 200.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 7,
                'code' => 'SYSTOUR007',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Cairo Islamic Architecture Tour',
                'overview' => 'Explore the magnificent Islamic architecture of Cairo. Visit historic mosques, madrasas, and Islamic monuments.',
                'slug' => 'cairo-islamic-architecture-tour',
                'duration_id' => 1,
                'adult_price' => 150.00,
                'child_price' => 100.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 8,
                'code' => 'SYSTOUR008',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Luxor East Bank Temples',
                'overview' => 'Visit the magnificent temples on Luxor\'s East Bank. Explore Karnak Temple Complex and Luxor Temple.',
                'slug' => 'luxor-east-bank-temples',
                'duration_id' => 1,
                'adult_price' => 200.00,
                'child_price' => 150.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 9,
                'code' => 'SYSTOUR009',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Aswan High Dam & Philae Temple',
                'overview' => 'Visit the engineering marvel of the High Dam and the beautiful Philae Temple. Learn about modern and ancient Egypt.',
                'slug' => 'aswan-high-dam-philae-temple',
                'duration_id' => 1,
                'adult_price' => 180.00,
                'child_price' => 120.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 10,
                'code' => 'SYSTOUR010',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Cairo Coptic Christian Heritage',
                'overview' => 'Explore the rich Christian heritage of Egypt. Visit the Hanging Church, St. Sergius Church, and Coptic Museum.',
                'slug' => 'cairo-coptic-christian-heritage',
                'duration_id' => 1,
                'adult_price' => 120.00,
                'child_price' => 80.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 11,
                'code' => 'SYSTOUR011',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Hurghada Desert Safari Adventure',
                'overview' => 'Experience the thrill of desert safari in Hurghada. Quad biking, camel riding, and traditional Bedouin dinner.',
                'slug' => 'hurghada-desert-safari-adventure',
                'duration_id' => 1,
                'adult_price' => 250.00,
                'child_price' => 180.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 12,
                'code' => 'SYSTOUR012',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Sharm El Sheikh Snorkeling',
                'overview' => 'Explore the vibrant coral reefs of Sharm El Sheikh. Snorkeling equipment and professional guides included.',
                'slug' => 'sharm-el-sheikh-snorkeling',
                'duration_id' => 1,
                'adult_price' => 160.00,
                'child_price' => 120.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 13,
                'code' => 'SYSTOUR013',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Cairo Museum & Antiquities',
                'overview' => 'Visit the world-famous Egyptian Museum and discover ancient treasures. See the treasures of Tutankhamun.',
                'slug' => 'cairo-museum-antiquities',
                'duration_id' => 1,
                'adult_price' => 100.00,
                'child_price' => 60.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 14,
                'code' => 'SYSTOUR014',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Luxor Hot Air Balloon Sunrise',
                'overview' => 'Float over the ancient monuments of Luxor in a hot air balloon. Breathtaking sunrise views of the Valley of the Kings.',
                'slug' => 'luxor-hot-air-balloon-sunrise',
                'duration_id' => 1,
                'adult_price' => 300.00,
                'child_price' => 200.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 15,
                'code' => 'SYSTOUR015',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Cairo Food & Culture Experience',
                'overview' => 'Experience authentic Egyptian cuisine and local culture. Taste traditional dishes and learn about Egyptian food traditions.',
                'slug' => 'cairo-food-culture-experience',
                'duration_id' => 9,
                'adult_price' => 80.00,
                'child_price' => 50.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 16,
                'code' => 'SYSTOUR016',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Nile Dinner Cruise & Entertainment',
                'overview' => 'Enjoy a romantic dinner cruise on the Nile with live entertainment. Experience Cairo\'s skyline from the water.',
                'slug' => 'nile-dinner-cruise-entertainment',
                'duration_id' => 10,
                'adult_price' => 180.00,
                'child_price' => 120.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => true,
                'display_order' => 17,
                'code' => 'SYSTOUR017',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Cairo Night Photography & Lights',
                'overview' => 'Capture stunning night photos of Cairo\'s illuminated landmarks. Professional photography guidance included.',
                'slug' => 'cairo-night-photography-lights',
                'duration_id' => 11,
                'adult_price' => 250.00,
                'child_price' => 180.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 18,
                'code' => 'SYSTOUR018',
                'duration_in_days' => 1,
            ],
            [
                'title' => 'Dendera & Abydos Temple Heritage',
                'overview' => 'Visit the well-preserved temples of Dendera and Abydos. Explore ancient Egyptian religious architecture.',
                'slug' => 'dendera-abydos-temple-heritage',
                'duration_id' => 3,
                'adult_price' => 320.00,
                'child_price' => 240.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 19,
                'code' => 'SYSTOUR019',
                'duration_in_days' => 2,
            ],
            [
                'title' => 'Fayoum Oasis Nature Discovery',
                'overview' => 'Explore the beautiful Fayoum Oasis, known for its waterfalls and natural beauty. Visit Wadi El-Rayan and Lake Qarun.',
                'slug' => 'fayoum-oasis-nature-discovery',
                'duration_id' => 1,
                'adult_price' => 220.00,
                'child_price' => 150.00,
                'infant_price' => 0.00,
                'enabled' => true,
                'featured' => false,
                'display_order' => 20,
                'code' => 'SYSTOUR020',
                'duration_in_days' => 1,
            ],
        ];

        foreach ($systemTours as $tourData) {
            // Check if tour already exists
            $existingTour = Tour::where('slug', $tourData['slug'])->first();
            
            if ($existingTour) {
                // Tour already exists, skip creation
                continue;
            }
            
            $durationId = $tourData['duration_id'];
            unset($tourData['duration_id']);

            $tour = Tour::create($tourData);
            
            // Associate tour with duration
            $duration = Duration::find($durationId);
            if ($duration) {
                $duration->tours()->attach($tour->id);
            }
        }

        // Update tours_count for all durations
        Duration::all()->each(function ($duration) {
            $duration->setToursCount();
        });

        $this->command->info('System Tours seeded successfully!');
    }
}

