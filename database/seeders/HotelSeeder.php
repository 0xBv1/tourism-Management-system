<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Amenity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = Amenity::all();

        $hotels = [
            [
                'name' => 'Luxury Resort & Spa',
                'description' => 'Experience ultimate luxury and relaxation at our premium resort and spa. Featuring world-class amenities and stunning views.',
                'city' => 'Cairo',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '123 Nile Corniche, Cairo',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'luxury-resort-spa',
                'phone_contact' => '+20-2-1234-5678',
                'whatsapp_contact' => '+20-10-1234-5678',
            ],
            [
                'name' => 'Business Center Hotel',
                'description' => 'Perfect for business travelers with modern amenities and central location. Close to major business districts.',
                'city' => 'Cairo',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '456 Maadi Corniche, Cairo',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'business-center-hotel',
                'phone_contact' => '+20-2-2345-6789',
                'whatsapp_contact' => '+20-10-2345-6789',
            ],
            [
                'name' => 'Mountain View Lodge',
                'description' => 'Rustic charm with breathtaking mountain views and outdoor activities. Perfect for nature lovers.',
                'city' => 'Sinai',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '789 Mount Sinai Road, Sinai',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'mountain-view-lodge',
                'phone_contact' => '+20-69-3456-7890',
                'whatsapp_contact' => '+20-10-3456-7890',
            ],
            [
                'name' => 'City Center Inn',
                'description' => 'Affordable comfort in the heart of the city with easy access to attractions. Great value for money.',
                'city' => 'Cairo',
                'stars' => 2,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '321 Tahrir Square, Cairo',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'city-center-inn',
                'phone_contact' => '+20-2-4567-8901',
                'whatsapp_contact' => '+20-10-4567-8901',
            ],
            [
                'name' => 'Seaside Boutique Hotel',
                'description' => 'Intimate boutique hotel with stunning ocean views and personalized service. Perfect for romantic getaways.',
                'city' => 'Hurghada',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '654 Red Sea Boulevard, Hurghada',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'seaside-boutique-hotel',
                'phone_contact' => '+20-65-5678-9012',
                'whatsapp_contact' => '+20-10-5678-9012',
            ],
            [
                'name' => 'Luxor Heritage Hotel',
                'description' => 'Experience ancient Egyptian luxury in the heart of Luxor. Overlooking the Nile and ancient temples.',
                'city' => 'Luxor',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '987 Nile Avenue, Luxor',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'luxor-heritage-hotel',
                'phone_contact' => '+20-95-6789-0123',
                'whatsapp_contact' => '+20-10-6789-0123',
            ],
            [
                'name' => 'Aswan Nile Palace',
                'description' => 'Elegant palace-style hotel on the banks of the Nile in Aswan. Spectacular views and traditional hospitality.',
                'city' => 'Aswan',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '456 Nile Palace Road, Aswan',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'aswan-nile-palace',
                'phone_contact' => '+20-97-7890-1234',
                'whatsapp_contact' => '+20-10-7890-1234',
            ],
            [
                'name' => 'Alexandria Mediterranean Resort',
                'description' => 'Beachfront resort in Alexandria with Mediterranean charm and modern amenities. Perfect for family vacations.',
                'city' => 'Alexandria',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '789 Mediterranean Beach, Alexandria',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'alexandria-mediterranean-resort',
                'phone_contact' => '+20-3-8901-2345',
                'whatsapp_contact' => '+20-10-8901-2345',
            ],
            [
                'name' => 'Sharm El Sheikh Paradise',
                'description' => 'Luxury resort in Sharm El Sheikh with private beach access and world-class diving facilities.',
                'city' => 'Sharm El Sheikh',
                'stars' => 5,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '123 Paradise Bay, Sharm El Sheikh',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'sharm-el-sheikh-paradise',
                'phone_contact' => '+20-69-9012-3456',
                'whatsapp_contact' => '+20-10-9012-3456',
            ],
            [
                'name' => 'Dahab Desert Lodge',
                'description' => 'Eco-friendly lodge in Dahab with stunning desert views and authentic Bedouin hospitality.',
                'city' => 'Dahab',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '456 Desert Road, Dahab',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'dahab-desert-lodge',
                'phone_contact' => '+20-69-0123-4567',
                'whatsapp_contact' => '+20-10-0123-4567',
            ],
            [
                'name' => 'Siwa Oasis Retreat',
                'description' => 'Peaceful retreat in the magical Siwa Oasis. Traditional mud-brick architecture and natural hot springs.',
                'city' => 'Siwa',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '789 Oasis Road, Siwa',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'siwa-oasis-retreat',
                'phone_contact' => '+20-46-4567-8902',
                'whatsapp_contact' => '+20-10-4567-8902',
            ],
            [
                'name' => 'Port Said Harbor Hotel',
                'description' => 'Historic hotel overlooking the Suez Canal in Port Said. Rich maritime history and modern comfort.',
                'city' => 'Port Said',
                'stars' => 4,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '456 Canal Street, Port Said',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'port-said-harbor-hotel',
                'phone_contact' => '+20-66-5678-9013',
                'whatsapp_contact' => '+20-10-5678-9013',
            ],
            [
                'name' => 'Fayoum Lake Resort',
                'description' => 'Serene resort on the shores of Lake Qarun in Fayoum. Perfect for bird watching and nature lovers.',
                'city' => 'Fayoum',
                'stars' => 3,
                'enabled' => true,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '123 Lake Road, Fayoum',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'fayoum-lake-resort',
                'phone_contact' => '+20-84-6789-0124',
                'whatsapp_contact' => '+20-10-6789-0124',
            ],
            [
                'name' => 'Disabled Hotel Example',
                'description' => 'This hotel is disabled and should not be counted.',
                'city' => 'Cairo',
                'stars' => 3,
                'enabled' => false,
                'featured_image' => null,
                'banner' => null,
                'gallery' => [],
                'address' => '999 Disabled Street, Cairo',
                'map_iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'slug' => 'disabled-hotel-example',
                'phone_contact' => '+20-2-9999-9999',
                'whatsapp_contact' => '+20-10-9999-9999',
            ],
        ];

        foreach ($hotels as $hotelData) {
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

        $this->command->info('Hotels seeded successfully!');
    }
}

