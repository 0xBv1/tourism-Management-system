<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Duration;
use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\User;
use App\Models\City;
use App\Models\Amenity;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive data seeding...');

        // Seed durations first
        $this->seedDurations();
        
        // Seed supplier users and suppliers
        $suppliers = $this->seedSuppliers();
        
        // Seed supplier services
        $this->seedSupplierHotels($suppliers);
        // Temporarily skip supplier tours due to model configuration issue
        // $this->seedSupplierTours($suppliers);
        $this->seedSupplierTrips($suppliers);
        $this->seedSupplierTransports($suppliers);

        $this->command->info('Comprehensive data seeding completed!');
    }

    /**
     * Seed durations
     */
    private function seedDurations(): void
    {
        $this->command->info('Seeding durations...');

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
                'title' => '5 Days 4 Nights',
                'description' => 'Ideal for exploring multiple destinations',
                'days' => 5,
                'nights' => 4,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => true,
                'display_order' => 4
            ],
            [
                'title' => '7 Days 6 Nights',
                'description' => 'A full week of adventure',
                'days' => 7,
                'nights' => 6,
                'duration_type' => 'days',
                'enabled' => true,
                'featured' => false,
                'display_order' => 5
            ],
            [
                'title' => '2 Hours',
                'description' => 'Quick tour or activity',
                'days' => 2,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 6
            ],
            [
                'title' => '4 Hours',
                'description' => 'Half-day experience',
                'days' => 4,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 7
            ],
            [
                'title' => '8 Hours',
                'description' => 'Full-day experience',
                'days' => 8,
                'nights' => null,
                'duration_type' => 'hours',
                'enabled' => true,
                'featured' => false,
                'display_order' => 8
            ]
        ];

        foreach ($durations as $durationData) {
            $duration = Duration::updateOrCreate(
                ['slug' => Str::slug($durationData['title'])],
                [
                    'enabled' => $durationData['enabled'],
                    'featured' => $durationData['featured'],
                    'display_order' => $durationData['display_order'],
                    'days' => $durationData['days'],
                    'nights' => $durationData['nights'],
                    'duration_type' => $durationData['duration_type'],
                ]
            );

            // Add translations
            $duration->translateOrNew('en')->title = $durationData['title'];
            $duration->translateOrNew('en')->description = $durationData['description'];
            $duration->save();

            $this->command->info("Created/Updated duration: {$durationData['title']}");
        }
    }

    /**
     * Seed suppliers
     */
    private function seedSuppliers(): array
    {
        $this->command->info('Seeding suppliers...');

        $suppliers = [
            [
                'name' => 'Cairo Travel Agency',
                'email' => 'info@cairotravel.com',
                'company_name' => 'Cairo Travel Agency',
                'company_email' => 'info@cairotravel.com',
                'phone' => '+201234567890',
                'address' => '123 Nile Street, Cairo, Egypt',
                'commission_rate' => 15.00,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Luxor Heritage Tours',
                'email' => 'info@luxorheritage.com',
                'company_name' => 'Luxor Heritage Tours',
                'company_email' => 'info@luxorheritage.com',
                'phone' => '+209512345678',
                'address' => '456 Karnak Road, Luxor, Egypt',
                'commission_rate' => 18.00,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Red Sea Adventures',
                'email' => 'info@redseaadventures.com',
                'company_name' => 'Red Sea Adventures',
                'company_email' => 'info@redseaadventures.com',
                'phone' => '+206512345678',
                'address' => '789 Hurghada Boulevard, Hurghada, Egypt',
                'commission_rate' => 12.00,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Alexandria Coastal Tours',
                'email' => 'info@alexandriacoastal.com',
                'company_name' => 'Alexandria Coastal Tours',
                'company_email' => 'info@alexandriacoastal.com',
                'phone' => '+203123456789',
                'address' => '321 Mediterranean Street, Alexandria, Egypt',
                'commission_rate' => 14.00,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sinai Desert Expeditions',
                'email' => 'info@sinaiexpeditions.com',
                'company_name' => 'Sinai Desert Expeditions',
                'company_email' => 'info@sinaiexpeditions.com',
                'phone' => '+206912345678',
                'address' => '654 Mount Sinai Road, Sinai, Egypt',
                'commission_rate' => 20.00,
                'is_verified' => true,
                'is_active' => true,
            ]
        ];

        $createdSuppliers = [];

        foreach ($suppliers as $supplierData) {
            // Create user first
            $user = User::updateOrCreate(
                ['email' => $supplierData['email']],
                [
                    'name' => $supplierData['name'],
                    'email' => $supplierData['email'],
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign supplier role
            $user->assignRole('Supplier');

            // Create supplier
            $supplier = Supplier::updateOrCreate(
                ['company_email' => $supplierData['company_email']],
                [
                    'user_id' => $user->id,
                    'company_name' => $supplierData['company_name'],
                    'company_email' => $supplierData['company_email'],
                    'phone' => $supplierData['phone'],
                    'address' => $supplierData['address'],
                    'commission_rate' => $supplierData['commission_rate'],
                    'is_verified' => $supplierData['is_verified'],
                    'is_active' => $supplierData['is_active'],
                    'verified_at' => now(),
                ]
            );

            $createdSuppliers[] = $supplier;
            $this->command->info("Created/Updated supplier: {$supplierData['company_name']}");
        }

        return $createdSuppliers;
    }

    /**
     * Seed supplier hotels
     */
    private function seedSupplierHotels(array $suppliers): void
    {
        $this->command->info('Seeding supplier hotels...');

        $hotels = [
            [
                'name' => 'Luxury Cairo Palace Hotel',
                'description' => 'Experience ultimate luxury in the heart of Cairo with stunning Nile views',
                'address' => '123 Nile Corniche, Cairo',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'phone' => '+201234567890',
                'email' => 'info@cairopalace.com',
                'website' => 'https://cairopalace.com',
                'stars' => 5,
                'price_per_night' => 450.00,
                'currency' => 'EGP',
                'amenities' => ['WiFi', 'Pool', 'Spa', 'Gym', 'Restaurant', 'Bar'],
                'supplier_index' => 0,
            ],
            [
                'name' => 'Luxor Heritage Resort',
                'description' => 'Ancient Egyptian luxury overlooking the Nile and temples',
                'address' => '456 Karnak Road, Luxor',
                'city' => 'Luxor',
                'country' => 'Egypt',
                'phone' => '+209512345678',
                'email' => 'info@luxorheritage.com',
                'website' => 'https://luxorheritage.com',
                'stars' => 4,
                'price_per_night' => 380.00,
                'currency' => 'EGP',
                'amenities' => ['WiFi', 'Pool', 'Garden', 'Restaurant', 'Nile View'],
                'supplier_index' => 1,
            ],
            [
                'name' => 'Red Sea Paradise Hotel',
                'description' => 'Beachfront paradise with crystal clear waters',
                'address' => '789 Hurghada Boulevard, Hurghada',
                'city' => 'Hurghada',
                'country' => 'Egypt',
                'phone' => '+206512345678',
                'email' => 'info@redseaparadise.com',
                'website' => 'https://redseaparadise.com',
                'stars' => 4,
                'price_per_night' => 320.00,
                'currency' => 'EGP',
                'amenities' => ['WiFi', 'Beach Access', 'Water Sports', 'Restaurant', 'Bar'],
                'supplier_index' => 2,
            ],
            [
                'name' => 'Alexandria Coastal Inn',
                'description' => 'Charming boutique hotel with Mediterranean views',
                'address' => '321 Mediterranean Street, Alexandria',
                'city' => 'Alexandria',
                'country' => 'Egypt',
                'phone' => '+203123456789',
                'email' => 'info@alexandriacoastal.com',
                'website' => 'https://alexandriacoastal.com',
                'stars' => 3,
                'price_per_night' => 280.00,
                'currency' => 'EGP',
                'amenities' => ['WiFi', 'Sea View', 'Restaurant', 'Garden'],
                'supplier_index' => 3,
            ],
            [
                'name' => 'Sinai Desert Lodge',
                'description' => 'Authentic Bedouin experience in the desert',
                'address' => '654 Mount Sinai Road, Sinai',
                'city' => 'Sinai',
                'country' => 'Egypt',
                'phone' => '+206912345678',
                'email' => 'info@sinailodge.com',
                'website' => 'https://sinailodge.com',
                'stars' => 3,
                'price_per_night' => 220.00,
                'currency' => 'EGP',
                'amenities' => ['WiFi', 'Desert Tours', 'Campfire', 'Traditional Food'],
                'supplier_index' => 4,
            ]
        ];

        foreach ($hotels as $hotelData) {
            $supplier = $suppliers[$hotelData['supplier_index']];

            // Create or update the hotel record
            $hotel = SupplierHotel::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'slug' => \Str::slug($hotelData['name'])
                ],
                [
                    'supplier_id' => $supplier->id,
                    'address' => $hotelData['address'],
                    'stars' => $hotelData['stars'],
                    'enabled' => true,
                    'approved' => true,
                    'slug' => \Str::slug($hotelData['name']),
                ]
            );

            // Add translations
            $hotel->translateOrNew('en')->name = $hotelData['name'];
            $hotel->translateOrNew('en')->description = $hotelData['description'];
            $hotel->translateOrNew('en')->city = $hotelData['city'];
            $hotel->save();

            $this->command->info("Created/Updated supplier hotel: {$hotelData['name']} for {$supplier->company_name}");
        }
    }

    /**
     * Seed supplier tours
     */
    private function seedSupplierTours(array $suppliers): void
    {
        $this->command->info('Seeding supplier tours...');

        $tours = [
            [
                'title' => 'Cairo Pyramids & Sphinx Tour',
                'description' => 'Explore the ancient wonders of Egypt with our comprehensive Cairo tour',
                'highlights' => 'Visit Great Pyramid, Sphinx, Egyptian Museum',
                'included' => 'Transport, Guide, Lunch, Entrance Fees',
                'excluded' => 'Personal Expenses, Tips',
                'duration' => '8 Hours',
                'type' => 'Cultural',
                'pickup_location' => 'Cairo Hotels',
                'dropoff_location' => 'Cairo Hotels',
                'adult_price' => 180.00,
                'child_price' => 120.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 15,
                'supplier_index' => 0,
            ],
            [
                'title' => 'Luxor Valley of Kings Adventure',
                'description' => 'Discover the tombs of ancient pharaohs in the Valley of the Kings',
                'highlights' => 'Valley of Kings, Hatshepsut Temple, Colossi of Memnon',
                'included' => 'Transport, Guide, Lunch, Entrance Fees',
                'excluded' => 'Personal Expenses, Tips',
                'duration' => '10 Hours',
                'type' => 'Historical',
                'pickup_location' => 'Luxor Hotels',
                'dropoff_location' => 'Luxor Hotels',
                'adult_price' => 250.00,
                'child_price' => 180.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 12,
                'supplier_index' => 1,
            ],
            [
                'title' => 'Red Sea Snorkeling Adventure',
                'description' => 'Explore the underwater world of the Red Sea',
                'highlights' => 'Snorkeling, Coral Reefs, Marine Life',
                'included' => 'Equipment, Guide, Lunch, Boat Trip',
                'excluded' => 'Personal Expenses, Tips',
                'duration' => '6 Hours',
                'type' => 'Adventure',
                'pickup_location' => 'Hurghada Hotels',
                'dropoff_location' => 'Hurghada Hotels',
                'adult_price' => 150.00,
                'child_price' => 100.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 20,
                'supplier_index' => 2,
            ],
            [
                'title' => 'Alexandria Coastal Discovery',
                'description' => 'Explore the Mediterranean coast and ancient Alexandria',
                'highlights' => 'Library of Alexandria, Pompey\'s Pillar, Catacombs',
                'included' => 'Transport, Guide, Lunch, Entrance Fees',
                'excluded' => 'Personal Expenses, Tips',
                'duration' => '8 Hours',
                'type' => 'Cultural',
                'pickup_location' => 'Alexandria Hotels',
                'dropoff_location' => 'Alexandria Hotels',
                'adult_price' => 200.00,
                'child_price' => 140.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 15,
                'supplier_index' => 3,
            ],
            [
                'title' => 'Sinai Desert Safari',
                'description' => 'Adventure through the stunning Sinai desert landscape',
                'highlights' => 'Desert Safari, Bedouin Camp, Stargazing',
                'included' => 'Transport, Guide, Dinner, Camp Experience',
                'excluded' => 'Personal Expenses, Tips',
                'duration' => '12 Hours',
                'type' => 'Adventure',
                'pickup_location' => 'Sinai Hotels',
                'dropoff_location' => 'Sinai Hotels',
                'adult_price' => 300.00,
                'child_price' => 200.00,
                'infant_price' => 0.00,
                'currency' => 'EGP',
                'max_group_size' => 10,
                'supplier_index' => 4,
            ]
        ];

        foreach ($tours as $tourData) {
            $supplier = $suppliers[$tourData['supplier_index']];

            // Check if tour already exists
            $existingTour = SupplierTour::where('supplier_id', $supplier->id)
                ->where('slug', \Str::slug($tourData['title']))
                ->first();

            if ($existingTour) {
                $tour = $existingTour;
                $tour->update([
                    'title' => $tourData['title'],
                    'duration' => $tourData['duration'],
                    'type' => $tourData['type'],
                    'pickup_location' => $tourData['pickup_location'],
                    'dropoff_location' => $tourData['dropoff_location'],
                    'adult_price' => $tourData['adult_price'],
                    'child_price' => $tourData['child_price'],
                    'infant_price' => $tourData['infant_price'],
                    'max_group_size' => $tourData['max_group_size'],
                    'enabled' => true,
                    'approved' => true,
                ]);
            } else {
                // Create new tour
                $tour = SupplierTour::create([
                    'supplier_id' => $supplier->id,
                    'title' => $tourData['title'],
                    'duration' => $tourData['duration'],
                    'type' => $tourData['type'],
                    'pickup_location' => $tourData['pickup_location'],
                    'dropoff_location' => $tourData['dropoff_location'],
                    'adult_price' => $tourData['adult_price'],
                    'child_price' => $tourData['child_price'],
                    'infant_price' => $tourData['infant_price'],
                    'max_group_size' => $tourData['max_group_size'],
                    'enabled' => true,
                    'approved' => true,
                    'slug' => \Str::slug($tourData['title']),
                ]);
            }

            // Add translations for additional fields
            $tour->translateOrNew('en')->description = $tourData['description'];
            $tour->translateOrNew('en')->highlights = $tourData['highlights'];
            $tour->translateOrNew('en')->included = $tourData['included'];
            $tour->translateOrNew('en')->excluded = $tourData['excluded'];
            $tour->save();

            $this->command->info("Created/Updated supplier tour: {$tourData['title']} for {$supplier->company_name}");
        }
    }

    /**
     * Seed supplier trips
     */
    private function seedSupplierTrips(array $suppliers): void
    {
        $this->command->info('Seeding supplier trips...');

        $trips = [
            [
                'trip_name' => 'Cairo to Alexandria Express',
                'trip_type' => 'one_way',
                'departure_city' => 'Cairo',
                'arrival_city' => 'Alexandria',
                'travel_date' => Carbon::now()->addDays(7),
                'return_date' => null,
                'departure_time' => '08:00:00',
                'arrival_time' => '10:30:00',
                'seat_price' => 180.00,
                'total_seats' => 45,
                'available_seats' => 40,
                'additional_notes' => 'Premium service with WiFi and refreshments',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'supplier_index' => 0,
            ],
            [
                'trip_name' => 'Cairo to Luxor Heritage Tour',
                'trip_type' => 'round_trip',
                'departure_city' => 'Cairo',
                'arrival_city' => 'Luxor',
                'travel_date' => Carbon::now()->addDays(10),
                'return_date' => Carbon::now()->addDays(15),
                'departure_time' => '07:00:00',
                'arrival_time' => '12:00:00',
                'seat_price' => 320.00,
                'total_seats' => 35,
                'available_seats' => 30,
                'additional_notes' => 'Luxury coach with entertainment system',
                'amenities' => ['WiFi', 'Air Conditioning', 'Entertainment', 'Refreshments', 'Reclining Seats'],
                'supplier_index' => 1,
            ],
            [
                'trip_name' => 'Cairo to Hurghada Beach Express',
                'trip_type' => 'one_way',
                'departure_city' => 'Cairo',
                'arrival_city' => 'Hurghada',
                'travel_date' => Carbon::now()->addDays(3),
                'return_date' => null,
                'departure_time' => '09:00:00',
                'arrival_time' => '15:00:00',
                'seat_price' => 220.00,
                'total_seats' => 40,
                'available_seats' => 35,
                'additional_notes' => 'Direct route to Red Sea resort',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'supplier_index' => 2,
            ],
            [
                'trip_name' => 'Alexandria to Cairo Return',
                'trip_type' => 'round_trip',
                'departure_city' => 'Alexandria',
                'arrival_city' => 'Cairo',
                'travel_date' => Carbon::now()->addDays(5),
                'return_date' => Carbon::now()->addDays(8),
                'departure_time' => '10:00:00',
                'arrival_time' => '12:30:00',
                'seat_price' => 160.00,
                'total_seats' => 50,
                'available_seats' => 45,
                'additional_notes' => 'Comfortable journey with scenic views',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments'],
                'supplier_index' => 3,
            ],
            [
                'trip_name' => 'Cairo to Sinai Desert Adventure',
                'trip_type' => 'one_way',
                'departure_city' => 'Cairo',
                'arrival_city' => 'Sinai',
                'travel_date' => Carbon::now()->addDays(14),
                'return_date' => null,
                'departure_time' => '06:30:00',
                'arrival_time' => '14:00:00',
                'seat_price' => 280.00,
                'total_seats' => 30,
                'available_seats' => 25,
                'additional_notes' => 'Adventure trip to desert landscape',
                'amenities' => ['WiFi', 'Air Conditioning', 'Restroom', 'Refreshments', 'Desert Guide'],
                'supplier_index' => 4,
            ]
        ];

        foreach ($trips as $tripData) {
            $supplier = $suppliers[$tripData['supplier_index']];

            $trip = SupplierTrip::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'trip_name' => $tripData['trip_name']
                ],
                [
                    'supplier_id' => $supplier->id,
                    'trip_name' => $tripData['trip_name'],
                    'trip_type' => $tripData['trip_type'],
                    'departure_city' => $tripData['departure_city'],
                    'arrival_city' => $tripData['arrival_city'],
                    'travel_date' => $tripData['travel_date'],
                    'return_date' => $tripData['return_date'],
                    'departure_time' => $tripData['departure_time'],
                    'arrival_time' => $tripData['arrival_time'],
                    'seat_price' => $tripData['seat_price'],
                    'total_seats' => $tripData['total_seats'],
                    'available_seats' => $tripData['available_seats'],
                    'additional_notes' => $tripData['additional_notes'],
                    'amenities' => json_encode($tripData['amenities']),
                    'enabled' => true,
                    'approved' => true,
                ]
            );

            $this->command->info("Created/Updated supplier trip: {$tripData['trip_name']} for {$supplier->company_name}");
        }
    }

    /**
     * Seed supplier transports
     */
    private function seedSupplierTransports(array $suppliers): void
    {
        $this->command->info('Seeding supplier transports...');

        $transports = [
            [
                'name' => 'Luxury Airport Transfer',
                'description' => 'Premium airport transfer service with professional drivers',
                'origin_location' => 'Cairo International Airport',
                'destination_location' => 'Cairo City Center',
                'estimated_travel_time' => 45,
                'distance' => 25.5,
                'route_type' => 'airport_transfer',
                'price' => 150.00,
                'currency' => 'EGP',
                'vehicle_type' => 'Luxury Sedan',
                'seating_capacity' => 4,
                'amenities' => ['WiFi', 'Air Conditioning', 'Professional Driver', 'Meet & Greet'],
                'supplier_index' => 0,
            ],
            [
                'name' => 'Luxor Temple Shuttle',
                'description' => 'Convenient shuttle service between Luxor hotels and temples',
                'origin_location' => 'Luxor Hotels',
                'destination_location' => 'Karnak Temple Complex',
                'estimated_travel_time' => 20,
                'distance' => 8.0,
                'route_type' => 'shuttle',
                'price' => 80.00,
                'currency' => 'EGP',
                'vehicle_type' => 'Mini Bus',
                'seating_capacity' => 15,
                'amenities' => ['Air Conditioning', 'Guide', 'Hotel Pickup'],
                'supplier_index' => 1,
            ],
            [
                'name' => 'Hurghada Beach Transfer',
                'description' => 'Reliable transfer service to Red Sea beaches',
                'origin_location' => 'Hurghada Hotels',
                'destination_location' => 'Giftun Island',
                'estimated_travel_time' => 30,
                'distance' => 15.0,
                'route_type' => 'beach_transfer',
                'price' => 120.00,
                'currency' => 'EGP',
                'vehicle_type' => 'Speed Boat',
                'seating_capacity' => 12,
                'amenities' => ['Life Jackets', 'Refreshments', 'Beach Equipment'],
                'supplier_index' => 2,
            ],
            [
                'name' => 'Alexandria City Tour Transport',
                'description' => 'Comprehensive city tour with professional guide',
                'origin_location' => 'Alexandria Hotels',
                'destination_location' => 'Various City Attractions',
                'estimated_travel_time' => 240,
                'distance' => 35.0,
                'route_type' => 'city_tour',
                'price' => 200.00,
                'currency' => 'EGP',
                'vehicle_type' => 'Air-Conditioned Bus',
                'seating_capacity' => 25,
                'amenities' => ['WiFi', 'Air Conditioning', 'Professional Guide', 'Lunch'],
                'supplier_index' => 3,
            ],
            [
                'name' => 'Sinai Desert Safari Transport',
                'description' => 'Adventure transport for desert exploration',
                'origin_location' => 'Sinai Hotels',
                'destination_location' => 'Desert Safari Locations',
                'estimated_travel_time' => 180,
                'distance' => 50.0,
                'route_type' => 'desert_safari',
                'price' => 300.00,
                'currency' => 'EGP',
                'vehicle_type' => '4x4 Jeep',
                'seating_capacity' => 6,
                'amenities' => ['Desert Guide', 'Safety Equipment', 'Refreshments', 'Camping Gear'],
                'supplier_index' => 4,
            ]
        ];

        foreach ($transports as $transportData) {
            $supplier = $suppliers[$transportData['supplier_index']];

            $transport = SupplierTransport::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'origin_location' => $transportData['origin_location'],
                    'destination_location' => $transportData['destination_location']
                ],
                [
                    'supplier_id' => $supplier->id,
                    'origin_location' => $transportData['origin_location'],
                    'destination_location' => $transportData['destination_location'],
                    'estimated_travel_time' => $transportData['estimated_travel_time'],
                    'distance' => $transportData['distance'],
                    'route_type' => $transportData['route_type'],
                    'price' => $transportData['price'],
                    'currency' => $transportData['currency'],
                    'vehicle_type' => $transportData['vehicle_type'],
                    'seating_capacity' => $transportData['seating_capacity'],
                    'amenities' => json_encode($transportData['amenities']),
                    'enabled' => true,
                    'approved' => true,
                ]
            );

            $this->command->info("Created/Updated supplier transport: {$transportData['name']} for {$supplier->company_name}");
        }
    }
}

