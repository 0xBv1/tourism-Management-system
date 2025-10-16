<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\City;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all()->keyBy('name');
        
        $hotels = [
            // Cairo Hotels
            [
                'name' => 'Four Seasons Hotel Cairo at Nile Plaza',
                'description' => 'Luxury hotel overlooking the Nile River with world-class amenities and stunning views.',
                'address' => '1089 Corniche El Nil, Garden City, Cairo',
                'city_id' => $cities['Cairo']->id,
                'phone' => '+20 2 2791 7000',
                'email' => 'cairo@fourseasons.com',
                'website' => 'https://www.fourseasons.com/caironp/',
                'star_rating' => 5,
                'total_rooms' => 365,
                'available_rooms' => 320,
                'price_per_night' => 450.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Pet Friendly'
                ],
                'images' => [
                    'hotel1_exterior.jpg',
                    'hotel1_room.jpg',
                    'hotel1_pool.jpg',
                    'hotel1_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Premium location with Nile view rooms available'
            ],
            [
                'name' => 'Cairo Marriott Hotel & Omar Khayyam Casino',
                'description' => 'Historic palace hotel with modern amenities and casino facilities.',
                'address' => '16 Saray El Gezira Street, Zamalek, Cairo',
                'city_id' => $cities['Cairo']->id,
                'phone' => '+20 2 2728 3000',
                'email' => 'cairo@marriott.com',
                'website' => 'https://www.marriott.com/hotels/travel/caigh-cairo-marriott-hotel-and-omar-khayyam-casino/',
                'star_rating' => 5,
                'total_rooms' => 1082,
                'available_rooms' => 950,
                'price_per_night' => 280.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Casino',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle'
                ],
                'images' => [
                    'hotel2_exterior.jpg',
                    'hotel2_room.jpg',
                    'hotel2_casino.jpg',
                    'hotel2_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in',
                'notes' => 'Historic palace with casino and multiple dining options'
            ],
            [
                'name' => 'Kempinski Nile Hotel Cairo',
                'description' => 'Elegant hotel on the banks of the Nile with contemporary luxury.',
                'address' => '12 Ahmed Ragheb Street, Garden City, Cairo',
                'city_id' => $cities['Cairo']->id,
                'phone' => '+20 2 2798 8000',
                'email' => 'cairo@kempinski.com',
                'website' => 'https://www.kempinski.com/en/cairo/nile-hotel/',
                'star_rating' => 5,
                'total_rooms' => 191,
                'available_rooms' => 175,
                'price_per_night' => 320.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle'
                ],
                'images' => [
                    'hotel3_exterior.jpg',
                    'hotel3_room.jpg',
                    'hotel3_pool.jpg',
                    'hotel3_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Modern luxury with Nile views'
            ],
            
            // Luxor Hotels
            [
                'name' => 'Sofitel Winter Palace Luxor',
                'description' => 'Historic Victorian palace hotel overlooking the Nile and Valley of the Kings.',
                'address' => 'Corniche El Nile Street, Luxor',
                'city_id' => $cities['Luxor']->id,
                'phone' => '+20 95 238 0425',
                'email' => 'luxor@sofitel.com',
                'website' => 'https://www.sofitel.com/gb/hotel-1400-sofitel-winter-palace-luxor/index.shtml',
                'star_rating' => 5,
                'total_rooms' => 92,
                'available_rooms' => 85,
                'price_per_night' => 180.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Garden'
                ],
                'images' => [
                    'hotel4_exterior.jpg',
                    'hotel4_room.jpg',
                    'hotel4_garden.jpg',
                    'hotel4_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Historic palace with beautiful gardens'
            ],
            [
                'name' => 'Hilton Luxor Resort & Spa',
                'description' => 'Modern resort hotel with comprehensive spa facilities and Nile views.',
                'address' => 'Karnak, Luxor',
                'city_id' => $cities['Luxor']->id,
                'phone' => '+20 95 237 4933',
                'email' => 'luxor@hilton.com',
                'website' => 'https://www.hilton.com/en/hotels/lxrhihi-hilton-luxor-resort-and-spa/',
                'star_rating' => 5,
                'total_rooms' => 319,
                'available_rooms' => 280,
                'price_per_night' => 150.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Kids Club'
                ],
                'images' => [
                    'hotel5_exterior.jpg',
                    'hotel5_room.jpg',
                    'hotel5_spa.jpg',
                    'hotel5_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in',
                'notes' => 'Modern resort with excellent spa facilities'
            ],
            
            // Aswan Hotels
            [
                'name' => 'Sofitel Legend Old Cataract Aswan',
                'description' => 'Luxury hotel with historic charm and stunning Nile views.',
                'address' => 'Abtal El Tahrir Street, Aswan',
                'city_id' => $cities['Aswan']->id,
                'phone' => '+20 97 231 6000',
                'email' => 'aswan@sofitel.com',
                'website' => 'https://www.sofitel.com/gb/hotel-1401-sofitel-legend-old-cataract-aswan/index.shtml',
                'star_rating' => 5,
                'total_rooms' => 138,
                'available_rooms' => 125,
                'price_per_night' => 220.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Historic Building'
                ],
                'images' => [
                    'hotel6_exterior.jpg',
                    'hotel6_room.jpg',
                    'hotel6_pool.jpg',
                    'hotel6_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Historic luxury hotel with Agatha Christie connections'
            ],
            
            // Hurghada Hotels
            [
                'name' => 'Grand Resort Hurghada',
                'description' => 'All-inclusive beachfront resort with multiple pools and water activities.',
                'address' => 'Sahl Hasheesh, Hurghada',
                'city_id' => $cities['Hurghada']->id,
                'phone' => '+20 65 346 0000',
                'email' => 'hurghada@grandresort.com',
                'website' => 'https://www.grandresort.com/hurghada',
                'star_rating' => 5,
                'total_rooms' => 500,
                'available_rooms' => 450,
                'price_per_night' => 120.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Water Sports',
                    'Kids Club',
                    'All Inclusive'
                ],
                'images' => [
                    'hotel7_exterior.jpg',
                    'hotel7_room.jpg',
                    'hotel7_beach.jpg',
                    'hotel7_pool.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in',
                'notes' => 'All-inclusive beachfront resort'
            ],
            [
                'name' => 'Steigenberger Hotel El Tahrir Hurghada',
                'description' => 'Modern beachfront hotel with excellent diving facilities.',
                'address' => 'El Tahrir Square, Hurghada',
                'city_id' => $cities['Hurghada']->id,
                'phone' => '+20 65 344 0000',
                'email' => 'hurghada@steigenberger.com',
                'website' => 'https://www.steigenberger.com/en/hotels/all-hotels/egypt/hurghada/steigenberger-hotel-el-tahrir',
                'star_rating' => 4,
                'total_rooms' => 300,
                'available_rooms' => 275,
                'price_per_night' => 90.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Diving Center'
                ],
                'images' => [
                    'hotel8_exterior.jpg',
                    'hotel8_room.jpg',
                    'hotel8_beach.jpg',
                    'hotel8_diving.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Great for diving enthusiasts'
            ],
            
            // Sharm El Sheikh Hotels
            [
                'name' => 'Four Seasons Resort Sharm El Sheikh',
                'description' => 'Luxury beachfront resort with world-class diving and spa facilities.',
                'address' => '1 Four Seasons Boulevard, Sharm El Sheikh',
                'city_id' => $cities['Sharm El Sheikh']->id,
                'phone' => '+20 69 360 3555',
                'email' => 'sharm@fourseasons.com',
                'website' => 'https://www.fourseasons.com/sharmelsheikh/',
                'star_rating' => 5,
                'total_rooms' => 136,
                'available_rooms' => 120,
                'price_per_night' => 400.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Diving Center',
                    'Kids Club'
                ],
                'images' => [
                    'hotel9_exterior.jpg',
                    'hotel9_room.jpg',
                    'hotel9_beach.jpg',
                    'hotel9_spa.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Luxury resort with excellent diving'
            ],
            [
                'name' => 'Rixos Sharm El Sheikh',
                'description' => 'All-inclusive luxury resort with multiple restaurants and entertainment.',
                'address' => 'Om El Seid Hill, Sharm El Sheikh',
                'city_id' => $cities['Sharm El Sheikh']->id,
                'phone' => '+20 69 360 0000',
                'email' => 'sharm@rixos.com',
                'website' => 'https://www.rixos.com/en/hotels/sharm-el-sheikh',
                'star_rating' => 5,
                'total_rooms' => 400,
                'available_rooms' => 350,
                'price_per_night' => 180.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Entertainment',
                    'Kids Club',
                    'All Inclusive'
                ],
                'images' => [
                    'hotel10_exterior.jpg',
                    'hotel10_room.jpg',
                    'hotel10_beach.jpg',
                    'hotel10_entertainment.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in',
                'notes' => 'All-inclusive with entertainment'
            ],
            
            // Alexandria Hotels
            [
                'name' => 'Four Seasons Hotel Alexandria at San Stefano',
                'description' => 'Luxury hotel with Mediterranean views and historic charm.',
                'address' => '399 El Geish Road, San Stefano, Alexandria',
                'city_id' => $cities['Alexandria']->id,
                'phone' => '+20 3 581 8000',
                'email' => 'alexandria@fourseasons.com',
                'website' => 'https://www.fourseasons.com/alexandria/',
                'star_rating' => 5,
                'total_rooms' => 118,
                'available_rooms' => 105,
                'price_per_night' => 250.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Mediterranean Views'
                ],
                'images' => [
                    'hotel11_exterior.jpg',
                    'hotel11_room.jpg',
                    'hotel11_pool.jpg',
                    'hotel11_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Mediterranean views and historic location'
            ],
            
            // Dahab Hotels
            [
                'name' => 'Hilton Dahab Resort',
                'description' => 'Beachfront resort perfect for diving and water sports.',
                'address' => 'Dahab Bay, Dahab',
                'city_id' => $cities['Dahab']->id,
                'phone' => '+20 69 364 0310',
                'email' => 'dahab@hilton.com',
                'website' => 'https://www.hilton.com/en/hotels/dahhihi-hilton-dahab-resort/',
                'star_rating' => 4,
                'total_rooms' => 200,
                'available_rooms' => 180,
                'price_per_night' => 80.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Diving Center',
                    'Water Sports'
                ],
                'images' => [
                    'hotel12_exterior.jpg',
                    'hotel12_room.jpg',
                    'hotel12_beach.jpg',
                    'hotel12_diving.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Perfect for diving and water sports'
            ],
            
            // Marsa Alam Hotels
            [
                'name' => 'Steigenberger Coraya Beach Resort',
                'description' => 'All-inclusive beachfront resort with pristine coral reefs.',
                'address' => 'Marsa Alam, Red Sea',
                'city_id' => $cities['Marsa Alam']->id,
                'phone' => '+20 65 375 0000',
                'email' => 'marsaalam@steigenberger.com',
                'website' => 'https://www.steigenberger.com/en/hotels/all-hotels/egypt/marsa-alam/steigenberger-coraya-beach',
                'star_rating' => 5,
                'total_rooms' => 300,
                'available_rooms' => 270,
                'price_per_night' => 100.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Diving Center',
                    'Kids Club',
                    'All Inclusive'
                ],
                'images' => [
                    'hotel13_exterior.jpg',
                    'hotel13_room.jpg',
                    'hotel13_beach.jpg',
                    'hotel13_coral.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in',
                'notes' => 'Pristine coral reefs for diving'
            ],
            
            // El Gouna Hotels
            [
                'name' => 'Movenpick Resort El Gouna',
                'description' => 'Luxury resort in the Red Sea with multiple pools and lagoons.',
                'address' => 'El Gouna, Red Sea',
                'city_id' => $cities['El Gouna']->id,
                'phone' => '+20 65 358 0000',
                'email' => 'elgouna@movenpick.com',
                'website' => 'https://www.movenpick.com/en/middle-east/egypt/el-gouna/movenpick-resort-el-gouna',
                'star_rating' => 5,
                'total_rooms' => 400,
                'available_rooms' => 360,
                'price_per_night' => 140.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Private Beach',
                    'Spa & Wellness Center',
                    'Fitness Center',
                    'Multiple Restaurants',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Business Center',
                    'Parking',
                    'Airport Shuttle',
                    'Water Sports',
                    'Kids Club'
                ],
                'images' => [
                    'hotel14_exterior.jpg',
                    'hotel14_room.jpg',
                    'hotel14_lagoon.jpg',
                    'hotel14_restaurant.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 24 hours before check-in',
                'notes' => 'Beautiful lagoons and water activities'
            ],
            
            // Siwa Hotels
            [
                'name' => 'Adrère Amellal Desert Ecolodge',
                'description' => 'Unique eco-lodge in the Siwa Oasis with traditional architecture.',
                'address' => 'Siwa Oasis, Western Desert',
                'city_id' => $cities['Siwa']->id,
                'phone' => '+20 46 460 0860',
                'email' => 'siwa@adrereamellal.com',
                'website' => 'https://www.adrereamellal.com',
                'star_rating' => 4,
                'total_rooms' => 40,
                'available_rooms' => 35,
                'price_per_night' => 200.00,
                'currency' => 'USD',
                'amenities' => [
                    'Free WiFi',
                    'Swimming Pool',
                    'Spa & Wellness Center',
                    'Restaurant',
                    'Bar',
                    'Room Service',
                    'Concierge',
                    'Parking',
                    'Airport Shuttle',
                    'Eco-Friendly',
                    'Traditional Architecture'
                ],
                'images' => [
                    'hotel15_exterior.jpg',
                    'hotel15_room.jpg',
                    'hotel15_oasis.jpg',
                    'hotel15_traditional.jpg'
                ],
                'status' => ResourceStatus::AVAILABLE,
                'active' => true,
                'enabled' => true,
                'check_in_time' => '15:00',
                'check_out_time' => '12:00',
                'cancellation_policy' => 'Free cancellation up to 72 hours before check-in',
                'notes' => 'Unique eco-lodge in the desert oasis'
            ]
        ];

        foreach ($hotels as $hotelData) {
            // Check if hotel already exists
            $existingHotel = Hotel::where('name', $hotelData['name'])->first();
            
            if (!$existingHotel) {
                Hotel::create($hotelData);
            }
        }

        $this->command->info('Hotels seeded successfully!');
    }
}

