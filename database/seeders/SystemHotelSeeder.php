<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemHotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = Amenity::all();

        $systemHotels = [
            [
                'name' => 'Cairo Grand Plaza Hotel',
                'description' => 'Luxury 5-star hotel in the heart of Cairo with stunning Nile views and world-class amenities.',
                'city' => 'Cairo',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '15 Tahrir Square, Downtown Cairo',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d31.2356!3d30.0444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1458228b8b8b8b8b%3A0x8b8b8b8b8b8b8b8b!2sCairo+Grand+Plaza+Hotel!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'cairo-grand-plaza-hotel',
                'phone_contact' => '+20-2-1234-5678',
                'whatsapp_contact' => '+20-10-1234-5678',
            ],
            [
                'name' => 'Luxor Nile Palace',
                'description' => 'Elegant palace-style hotel overlooking the Nile in Luxor, perfect for exploring ancient temples.',
                'city' => 'Luxor',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '25 Nile Corniche, Luxor',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d32.6396!3d25.6872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sLuxor+Nile+Palace!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'luxor-nile-palace',
                'phone_contact' => '+20-95-2345-6789',
                'whatsapp_contact' => '+20-10-2345-6789',
            ],
            [
                'name' => 'Aswan Cataract Hotel',
                'description' => 'Historic hotel with breathtaking views of the Nile and Elephantine Island in Aswan.',
                'city' => 'Aswan',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '35 Nile Street, Aswan',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d32.8996!3d24.0889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sAswan+Cataract+Hotel!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'aswan-cataract-hotel',
                'phone_contact' => '+20-97-3456-7890',
                'whatsapp_contact' => '+20-10-3456-7890',
            ],
            [
                'name' => 'Alexandria Mediterranean Resort',
                'description' => 'Beachfront resort in Alexandria with Mediterranean charm and modern luxury.',
                'city' => 'Alexandria',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '45 Corniche Road, Alexandria',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d29.9186!3d31.2001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sAlexandria+Mediterranean+Resort!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'alexandria-mediterranean-resort',
                'phone_contact' => '+20-3-4567-8901',
                'whatsapp_contact' => '+20-10-4567-8901',
            ],
            [
                'name' => 'Hurghada Red Sea Resort',
                'description' => 'All-inclusive resort on the Red Sea coast with diving and water sports facilities.',
                'city' => 'Hurghada',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '55 Red Sea Boulevard, Hurghada',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d33.8126!3d27.2579!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sHurghada+Red+Sea+Resort!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'hurghada-red-sea-resort',
                'phone_contact' => '+20-65-5678-9012',
                'whatsapp_contact' => '+20-10-5678-9012',
            ],
            [
                'name' => 'Sharm El Sheikh Paradise',
                'description' => 'Luxury resort in Sharm El Sheikh with private beach and world-class diving.',
                'city' => 'Sharm El Sheikh',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '65 Naama Bay, Sharm El Sheikh',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d34.3616!3d27.9158!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sSharm+El+Sheikh+Paradise!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'sharm-el-sheikh-paradise',
                'phone_contact' => '+20-69-6789-0123',
                'whatsapp_contact' => '+20-10-6789-0123',
            ],
            [
                'name' => 'Dahab Beach Hotel',
                'description' => 'Charming beachfront hotel in Dahab known for its laid-back atmosphere and diving.',
                'city' => 'Dahab',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '75 Lighthouse Bay, Dahab',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d34.5166!3d28.5092!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sDahab+Beach+Hotel!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'dahab-beach-hotel',
                'phone_contact' => '+20-69-7890-1234',
                'whatsapp_contact' => '+20-10-7890-1234',
            ],
            [
                'name' => 'Giza Pyramids View Hotel',
                'description' => 'Unique hotel with direct views of the Great Pyramids of Giza.',
                'city' => 'Giza',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '85 Pyramids Road, Giza',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d31.1342!3d29.9792!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sGiza+Pyramids+View+Hotel!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'giza-pyramids-view-hotel',
                'phone_contact' => '+20-2-8901-2345',
                'whatsapp_contact' => '+20-10-8901-2345',
            ],
            [
                'name' => 'Marsa Alam Desert Lodge',
                'description' => 'Desert lodge in Marsa Alam offering authentic Bedouin experience.',
                'city' => 'Marsa Alam',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '95 Desert Road, Marsa Alam',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d36.6876!3d25.0669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sMarsa+Alam+Desert+Lodge!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'marsa-alam-desert-lodge',
                'phone_contact' => '+20-65-9012-3456',
                'whatsapp_contact' => '+20-10-9012-3456',
            ],
            [
                'name' => 'El Gouna Beach Club',
                'description' => 'Exclusive beach club in El Gouna with private beach access.',
                'city' => 'El Gouna',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '105 Marina Road, El Gouna',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.123456789!2d33.6789!3d27.3947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x144916a8b8b8b8b8%3A0x8b8b8b8b8b8b8b8b!2sEl+Gouna+Beach+Club!5e0!3m2!1sen!2seg!4v1234567890" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'el-gouna-beach-club',
                'phone_contact' => '+20-65-0123-4567',
                'whatsapp_contact' => '+20-10-0123-4567',
            ],
        ];

        foreach ($systemHotels as $hotelData) {
            // Check if hotel already exists
            $existingHotel = Hotel::where('slug', $hotelData['slug'])->first();
            
            if ($existingHotel) {
                // Hotel already exists, skip creation
                continue;
            }
            
            // Create hotel
            $hotel = Hotel::create([
                'stars' => $hotelData['stars'],
                'enabled' => $hotelData['enabled'],
                'featured_image' => $hotelData['featured_image'],
                'banner' => $hotelData['banner'],
                'gallery' => $hotelData['gallery'],
                'address' => $hotelData['address'],
                'map_iframe' => $hotelData['map_iframe'],
                'slug' => $hotelData['slug'],
                'phone_contact' => $hotelData['phone_contact'],
                'whatsapp_contact' => $hotelData['whatsapp_contact'],
            ]);

            // Create hotel translation
            DB::table('hotel_translations')->insert([
                'hotel_id' => $hotel->id,
                'locale' => 'en',
                'name' => $hotelData['name'],
                'description' => $hotelData['description'],
                'city' => $hotelData['city'],
            ]);
            
            // Attach random amenities to each hotel
            $randomAmenities = $amenities->random(rand(3, 8));
            $hotel->amenities()->attach($randomAmenities->pluck('id')->toArray());
        }

        $this->command->info('System Hotels seeded successfully!');
    }
}

