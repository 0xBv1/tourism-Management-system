<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Top 10 Must-Visit Pyramids in Egypt',
                'description' => 'Discover the most impressive pyramids in Egypt, from the iconic Great Pyramid of Giza to hidden gems in the desert. Learn about their history, architecture, and the best times to visit.',
                'tags' => 'pyramids, ancient egypt, giza, history, archaeology',
                'slug' => 'top-10-must-visit-pyramids-egypt',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'display_order' => 1,
            ],
            [
                'title' => 'Ultimate Guide to Nile River Cruises',
                'description' => 'Everything you need to know about cruising the Nile River. From luxury ships to traditional feluccas, discover the best routes, seasons, and what to expect on your journey.',
                'tags' => 'nile cruise, river cruise, luxor, aswan, egypt travel',
                'slug' => 'ultimate-guide-nile-river-cruises',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'display_order' => 2,
            ],
            [
                'title' => 'Best Time to Visit Egypt: Weather Guide',
                'description' => 'Plan your perfect trip to Egypt with our comprehensive weather guide. Learn about the best seasons for different activities, from exploring ancient sites to diving in the Red Sea.',
                'tags' => 'weather, best time, seasons, travel planning, egypt climate',
                'slug' => 'best-time-visit-egypt-weather-guide',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'display_order' => 3,
            ],
            [
                'title' => 'Hidden Gems of Alexandria: Beyond the Library',
                'description' => 'Explore the lesser-known attractions of Alexandria, Egypt\'s Mediterranean pearl. From ancient catacombs to charming cafes, discover the city\'s hidden treasures.',
                'tags' => 'alexandria, mediterranean, catacombs, pompeys pillar, hidden gems',
                'slug' => 'hidden-gems-alexandria-beyond-library',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'display_order' => 4,
            ],
            [
                'title' => 'Red Sea Diving: World\'s Best Underwater Paradise',
                'description' => 'Dive into the crystal-clear waters of the Red Sea and discover some of the world\'s most spectacular coral reefs and marine life. Complete guide for divers of all levels.',
                'tags' => 'red sea, diving, coral reefs, marine life, hurghada, sharm el sheikh',
                'slug' => 'red-sea-diving-worlds-best-underwater-paradise',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(18),
                'display_order' => 5,
            ],
            [
                'title' => 'Luxor Temple Complex: A Journey Through Time',
                'description' => 'Step back in time as we explore the magnificent Luxor Temple and Karnak Complex. Learn about the pharaohs who built these incredible structures and the religious significance they held.',
                'tags' => 'luxor temple, karnak, ancient egypt, pharaohs, thebes',
                'slug' => 'luxor-temple-complex-journey-through-time',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(22),
                'display_order' => 6,
            ],
            [
                'title' => 'Egyptian Cuisine: A Food Lover\'s Guide',
                'description' => 'Savor the flavors of authentic Egyptian cuisine. From street food favorites to traditional dishes, discover the rich culinary heritage of the Nile Valley.',
                'tags' => 'egyptian food, cuisine, street food, traditional dishes, food guide',
                'slug' => 'egyptian-cuisine-food-lovers-guide',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(25),
                'display_order' => 7,
            ],
            [
                'title' => 'Abu Simbel: Ramses II\'s Monumental Temple',
                'description' => 'Marvel at the incredible Abu Simbel temples, built by Ramses II. Learn about the massive relocation project that saved these ancient wonders from the rising waters of Lake Nasser.',
                'tags' => 'abu simbel, ramses ii, lake nasser, temple relocation, ancient wonders',
                'slug' => 'abu-simbel-ramses-ii-monumental-temple',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(28),
                'display_order' => 8,
            ],
            [
                'title' => 'Egyptian Markets: A Shopper\'s Paradise',
                'description' => 'Navigate the bustling markets of Egypt, from the famous Khan el-Khalili in Cairo to local souks across the country. Learn bargaining tips and discover unique treasures.',
                'tags' => 'markets, khan el-khalili, souks, shopping, bargaining, local crafts',
                'slug' => 'egyptian-markets-shoppers-paradise',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(32),
                'display_order' => 9,
            ],
            [
                'title' => 'Valley of the Kings: Royal Burial Grounds',
                'description' => 'Explore the mysterious Valley of the Kings, where pharaohs were laid to rest in elaborate tombs. Discover the most impressive burial chambers and their fascinating history.',
                'tags' => 'valley of the kings, tombs, pharaohs, burial chambers, ancient egypt',
                'slug' => 'valley-kings-royal-burial-grounds',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(35),
                'display_order' => 10,
            ],
            [
                'title' => 'Egyptian Desert Adventures: Beyond the Pyramids',
                'description' => 'Venture into Egypt\'s vast deserts for unforgettable adventures. From camel treks to stargazing, discover the natural beauty and cultural experiences of the desert.',
                'tags' => 'desert adventures, camel treks, stargazing, western desert, siwa oasis',
                'slug' => 'egyptian-desert-adventures-beyond-pyramids',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(38),
                'display_order' => 11,
            ],
            [
                'title' => 'Islamic Cairo: A Walk Through History',
                'description' => 'Stroll through the historic Islamic quarter of Cairo and discover magnificent mosques, madrasas, and architectural wonders that tell the story of Islamic Egypt.',
                'tags' => 'islamic cairo, mosques, madrasas, islamic architecture, historic quarter',
                'slug' => 'islamic-cairo-walk-through-history',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(42),
                'display_order' => 12,
            ],
            [
                'title' => 'Egyptian Museum: Treasures of the Pharaohs',
                'description' => 'Explore the world-famous Egyptian Museum in Cairo, home to the largest collection of ancient Egyptian artifacts. See the treasures of Tutankhamun and other royal mummies.',
                'tags' => 'egyptian museum, tutankhamun, royal mummies, ancient artifacts, cairo',
                'slug' => 'egyptian-museum-treasures-pharaohs',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(45),
                'display_order' => 13,
            ],
            [
                'title' => 'Coptic Cairo: Christian Heritage of Egypt',
                'description' => 'Explore the rich Christian heritage of Egypt in Coptic Cairo. Visit the Hanging Church, St. Sergius Church, and learn about the history of Christianity in the Nile Valley.',
                'tags' => 'coptic cairo, christian heritage, hanging church, st sergius, religious history',
                'slug' => 'coptic-cairo-christian-heritage-egypt',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(48),
                'display_order' => 14,
            ],
            [
                'title' => 'Fayoum Oasis: Nature\'s Hidden Paradise',
                'description' => 'Escape to the beautiful Fayoum Oasis, home to Lake Qarun and stunning waterfalls. Discover the natural beauty and rich history of this lesser-known Egyptian destination.',
                'tags' => 'fayoum oasis, lake qarun, waterfalls, nature, bird watching',
                'slug' => 'fayoum-oasis-natures-hidden-paradise',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(52),
                'display_order' => 15,
            ],
            [
                'title' => 'Egyptian Festivals and Celebrations',
                'description' => 'Experience the vibrant culture of Egypt through its festivals and celebrations. From religious holidays to cultural events, discover the traditions that make Egypt unique.',
                'tags' => 'egyptian festivals, celebrations, culture, traditions, holidays',
                'slug' => 'egyptian-festivals-celebrations',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(55),
                'display_order' => 16,
            ],
            [
                'title' => 'Sailing the Nile: Traditional Felucca Experience',
                'description' => 'Experience the timeless beauty of the Nile aboard a traditional felucca. Learn about these ancient sailing vessels and discover the best routes for a peaceful river journey.',
                'tags' => 'felucca, nile sailing, traditional boats, river journey, peaceful travel',
                'slug' => 'sailing-nile-traditional-felucca-experience',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(58),
                'display_order' => 17,
            ],
            [
                'title' => 'Egyptian Souvenirs: What to Buy and Where',
                'description' => 'Find the perfect souvenirs from your Egyptian adventure. From traditional crafts to modern gifts, discover the best shopping spots and authentic Egyptian products.',
                'tags' => 'souvenirs, shopping, egyptian crafts, traditional gifts, shopping guide',
                'slug' => 'egyptian-souvenirs-what-buy-where',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(62),
                'display_order' => 18,
            ],
            [
                'title' => 'Photography in Egypt: Capturing Ancient Wonders',
                'description' => 'Master the art of photography in Egypt\'s most photogenic locations. Get tips for capturing the pyramids, temples, and landscapes in their best light.',
                'tags' => 'photography, egypt photos, travel photography, pyramids, temples',
                'slug' => 'photography-egypt-capturing-ancient-wonders',
                'featured_image' => null,
                'gallery' => [],
                'active' => true,
                'status' => 'published',
                'published_at' => now()->subDays(65),
                'display_order' => 19,
            ],
            [
                'title' => 'Disabled Blog Example',
                'description' => 'This blog post is disabled and should not be counted.',
                'tags' => 'disabled, example',
                'slug' => 'disabled-blog-example',
                'featured_image' => null,
                'gallery' => [],
                'active' => false,
                'status' => 'drafted',
                'published_at' => null,
                'display_order' => 20,
            ],
        ];

        foreach ($blogs as $blogData) {
            // Check if blog already exists
            $existingBlog = Blog::where('slug', $blogData['slug'])->first();
            
            if ($existingBlog) {
                // Blog already exists, skip creation
                continue;
            }
            
            Blog::create($blogData);
        }

        $this->command->info('Blogs seeded successfully!');
    }
}

