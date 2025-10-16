<?php

namespace Database\Seeders;

use App\Models\ResourceBooking;
use App\Models\BookingFile;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Enums\BookingStatus;
use Illuminate\Database\Seeder;

class ResourceBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookingFiles = BookingFile::all();
        $hotels = Hotel::all();
        $vehicles = Vehicle::all();
        $guides = Guide::all();
        $representatives = Representative::all();
        
        if ($bookingFiles->isEmpty()) {
            $this->command->warn('No booking files found. Please run BookingFileSeeder first.');
            return;
        }
        
        $resourceBookings = [];
        
        foreach ($bookingFiles as $bookingFile) {
            $inquiry = $bookingFile->inquiry;
            $arrivalDate = $inquiry->arrival_date;
            $departureDate = $inquiry->departure_date;
            $numberPax = $inquiry->number_pax;
            
            // Generate hotel bookings
            if ($hotels->isNotEmpty()) {
                $hotelCount = rand(1, 3); // 1-3 hotels per booking
                for ($i = 0; $i < $hotelCount; $i++) {
                    $hotel = $hotels->random();
                    $checkIn = $arrivalDate->copy()->addDays($i * 2);
                    $checkOut = $checkIn->copy()->addDays(rand(1, 3));
                    
                    if ($checkOut->lte($departureDate)) {
                        $resourceBookings[] = [
                            'booking_file_id' => $bookingFile->id,
                            'resource_type' => 'hotel',
                            'resource_id' => $hotel->id,
                            'resource_name' => $hotel->name,
                            'start_date' => $checkIn,
                            'end_date' => $checkOut,
                            'quantity' => rand(1, 2), // 1-2 rooms
                            'unit_price' => $hotel->price_per_night,
                            'total_price' => $hotel->price_per_night * rand(1, 2) * $checkIn->diffInDays($checkOut),
                            'currency' => $hotel->currency,
                            'status' => $this->getResourceBookingStatus($bookingFile->status),
                            'notes' => "Hotel booking for {$numberPax} guests. Check-in: {$checkIn->format('Y-m-d')}, Check-out: {$checkOut->format('Y-m-d')}",
                            'special_requests' => $this->generateSpecialRequests('hotel'),
                            'confirmation_number' => 'HOTEL-' . strtoupper(uniqid()),
                            'cancellation_policy' => $hotel->cancellation_policy ?? 'Free cancellation up to 24 hours before check-in',
                        ];
                    }
                }
            }
            
            // Generate vehicle bookings
            if ($vehicles->isNotEmpty()) {
                $vehicleCount = rand(1, 2); // 1-2 vehicles per booking
                for ($i = 0; $i < $vehicleCount; $i++) {
                    $vehicle = $vehicles->random();
                    $startDate = $arrivalDate->copy()->addDays($i);
                    $endDate = $startDate->copy()->addDays(rand(1, 5));
                    
                    if ($endDate->lte($departureDate)) {
                        $days = $startDate->diffInDays($endDate);
                        $pricePerDay = $vehicle->price_per_day;
                        $totalPrice = $pricePerDay * $days;
                        
                        $resourceBookings[] = [
                            'booking_file_id' => $bookingFile->id,
                            'resource_type' => 'vehicle',
                            'resource_id' => $vehicle->id,
                            'resource_name' => $vehicle->name,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'quantity' => 1,
                            'unit_price' => $pricePerDay,
                            'total_price' => $totalPrice,
                            'currency' => $vehicle->currency,
                            'status' => $this->getResourceBookingStatus($bookingFile->status),
                            'notes' => "Vehicle rental: {$vehicle->name} ({$vehicle->type}) for {$days} days. Driver: {$vehicle->driver_name}",
                            'special_requests' => $this->generateSpecialRequests('vehicle'),
                            'confirmation_number' => 'VEHICLE-' . strtoupper(uniqid()),
                            'cancellation_policy' => 'Free cancellation up to 48 hours before rental start',
                        ];
                    }
                }
            }
            
            // Generate guide bookings
            if ($guides->isNotEmpty()) {
                $guideCount = rand(1, 2); // 1-2 guides per booking
                for ($i = 0; $i < $guideCount; $i++) {
                    $guide = $guides->random();
                    $startDate = $arrivalDate->copy()->addDays($i);
                    $endDate = $startDate->copy()->addDays(rand(1, 3));
                    
                    if ($endDate->lte($departureDate)) {
                        $days = $startDate->diffInDays($endDate);
                        $pricePerDay = $guide->price_per_day;
                        $totalPrice = $pricePerDay * $days;
                        
                        $resourceBookings[] = [
                            'booking_file_id' => $bookingFile->id,
                            'resource_type' => 'guide',
                            'resource_id' => $guide->id,
                            'resource_name' => $guide->name,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'quantity' => 1,
                            'unit_price' => $pricePerDay,
                            'total_price' => $totalPrice,
                            'currency' => $guide->currency,
                            'status' => $this->getResourceBookingStatus($bookingFile->status),
                            'notes' => "Professional guide: {$guide->name}. Specializations: " . implode(', ', $guide->specializations),
                            'special_requests' => $this->generateSpecialRequests('guide'),
                            'confirmation_number' => 'GUIDE-' . strtoupper(uniqid()),
                            'cancellation_policy' => 'Free cancellation up to 24 hours before tour start',
                        ];
                    }
                }
            }
            
            // Generate representative bookings
            if ($representatives->isNotEmpty()) {
                $repCount = rand(0, 1); // 0-1 representatives per booking
                if ($repCount > 0) {
                    $representative = $representatives->random();
                    $startDate = $arrivalDate;
                    $endDate = $departureDate;
                    
                    $days = $startDate->diffInDays($endDate);
                    $pricePerDay = $representative->price_per_day;
                    $totalPrice = $pricePerDay * $days;
                    
                    $resourceBookings[] = [
                        'booking_file_id' => $bookingFile->id,
                        'resource_type' => 'representative',
                        'resource_id' => $representative->id,
                        'resource_name' => $representative->name,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'quantity' => 1,
                        'unit_price' => $pricePerDay,
                        'total_price' => $totalPrice,
                        'currency' => $representative->currency,
                        'status' => $this->getResourceBookingStatus($bookingFile->status),
                        'notes' => "Tour representative: {$representative->name}. Company: {$representative->company_name}",
                        'special_requests' => $this->generateSpecialRequests('representative'),
                        'confirmation_number' => 'REP-' . strtoupper(uniqid()),
                        'cancellation_policy' => 'Free cancellation up to 48 hours before service start',
                    ];
                }
            }
        }
        
        // Add some additional resource bookings for different scenarios
        $additionalBookings = [
            [
                'booking_file_id' => $bookingFiles->random()->id,
                'resource_type' => 'hotel',
                'resource_id' => $hotels->random()->id,
                'resource_name' => 'Luxury Resort Package',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(15),
                'quantity' => 2,
                'unit_price' => 200.00,
                'total_price' => 2000.00,
                'currency' => 'USD',
                'status' => BookingStatus::PENDING,
                'notes' => 'Luxury resort package with all-inclusive amenities',
                'special_requests' => ['Ocean view room', 'Late checkout', 'Airport transfer'],
                'confirmation_number' => 'LUXURY-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 72 hours before check-in',
            ],
            [
                'booking_file_id' => $bookingFiles->random()->id,
                'resource_type' => 'vehicle',
                'resource_id' => $vehicles->random()->id,
                'resource_name' => 'VIP Transfer Service',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(5),
                'quantity' => 1,
                'unit_price' => 150.00,
                'total_price' => 150.00,
                'currency' => 'USD',
                'status' => BookingStatus::CONFIRMED,
                'notes' => 'VIP airport transfer service with luxury vehicle',
                'special_requests' => ['Meet and greet', 'Bottled water', 'WiFi'],
                'confirmation_number' => 'VIP-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 24 hours before service',
            ],
            [
                'booking_file_id' => $bookingFiles->random()->id,
                'resource_type' => 'guide',
                'resource_id' => $guides->random()->id,
                'resource_name' => 'Private Archaeological Tour',
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(22),
                'quantity' => 1,
                'unit_price' => 300.00,
                'total_price' => 900.00,
                'currency' => 'USD',
                'status' => BookingStatus::IN_PROGRESS,
                'notes' => 'Private archaeological tour with expert Egyptologist',
                'special_requests' => ['Early morning start', 'Photography permission', 'Academic materials'],
                'confirmation_number' => 'ARCH-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 48 hours before tour',
            ],
            [
                'booking_file_id' => $bookingFiles->random()->id,
                'resource_type' => 'representative',
                'resource_id' => $representatives->random()->id,
                'resource_name' => 'Full Service Representative',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(37),
                'quantity' => 1,
                'unit_price' => 100.00,
                'total_price' => 700.00,
                'currency' => 'USD',
                'status' => BookingStatus::COMPLETED,
                'notes' => 'Full service representative for complete tour duration',
                'special_requests' => ['24/7 availability', 'Multilingual support', 'Emergency assistance'],
                'confirmation_number' => 'FULL-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 72 hours before service',
            ]
        ];
        
        $allResourceBookings = array_merge($resourceBookings, $additionalBookings);
        
        foreach ($allResourceBookings as $bookingData) {
            ResourceBooking::create($bookingData);
        }

        $this->command->info('Resource bookings seeded successfully!');
    }
    
    private function getResourceBookingStatus($bookingFileStatus): BookingStatus
    {
        switch ($bookingFileStatus) {
            case 'pending':
                return BookingStatus::PENDING;
            case 'confirmed':
                return BookingStatus::CONFIRMED;
            case 'in_progress':
                return BookingStatus::IN_PROGRESS;
            case 'completed':
                return BookingStatus::COMPLETED;
            case 'cancelled':
                return BookingStatus::CANCELLED;
            case 'refunded':
                return BookingStatus::REFUNDED;
            default:
                return BookingStatus::PENDING;
        }
    }
    
    private function generateSpecialRequests(string $resourceType): array
    {
        $requests = [];
        
        switch ($resourceType) {
            case 'hotel':
                $hotelRequests = [
                    'High floor room',
                    'Ocean view',
                    'City view',
                    'Late checkout',
                    'Early check-in',
                    'Airport transfer',
                    'Room service',
                    'WiFi access',
                    'Extra bed',
                    'Non-smoking room'
                ];
                $requests = array_rand(array_flip($hotelRequests), rand(1, 3));
                break;
                
            case 'vehicle':
                $vehicleRequests = [
                    'Air conditioning',
                    'WiFi',
                    'Child seat',
                    'GPS navigation',
                    'Airport pickup',
                    'Meet and greet',
                    'Bottled water',
                    'Extra luggage space',
                    'Driver speaks English',
                    'Fuel included'
                ];
                $requests = array_rand(array_flip($vehicleRequests), rand(1, 3));
                break;
                
            case 'guide':
                $guideRequests = [
                    'Early morning start',
                    'Photography permission',
                    'Academic materials',
                    'Multilingual guide',
                    'Private tour',
                    'Custom itinerary',
                    'Transportation included',
                    'Meals included',
                    'Entrance fees included',
                    'Expert knowledge'
                ];
                $requests = array_rand(array_flip($guideRequests), rand(1, 3));
                break;
                
            case 'representative':
                $repRequests = [
                    '24/7 availability',
                    'Multilingual support',
                    'Emergency assistance',
                    'Airport assistance',
                    'Hotel check-in help',
                    'Restaurant reservations',
                    'Shopping assistance',
                    'Cultural insights',
                    'Local recommendations',
                    'Translation services'
                ];
                $requests = array_rand(array_flip($repRequests), rand(1, 3));
                break;
        }
        
        return is_array($requests) ? $requests : [$requests];
    }
}

