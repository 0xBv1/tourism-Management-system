<?php

namespace Database\Seeders;

use App\Models\InquiryResource;
use App\Models\Inquiry;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Enums\ResourceStatus;
use Illuminate\Database\Seeder;

class InquiryResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inquiries = Inquiry::all();
        $hotels = Hotel::all();
        $vehicles = Vehicle::all();
        $guides = Guide::all();
        $representatives = Representative::all();
        
        if ($inquiries->isEmpty()) {
            $this->command->warn('No inquiries found. Please run InquirySeeder first.');
            return;
        }
        
        $inquiryResources = [];
        
        foreach ($inquiries as $inquiry) {
            $arrivalDate = $inquiry->arrival_date;
            $departureDate = $inquiry->departure_date;
            $numberPax = $inquiry->number_pax;
            
            // Generate hotel resources for inquiries
            if ($hotels->isNotEmpty()) {
                $hotelCount = rand(1, 3); // 1-3 hotels per inquiry
                for ($i = 0; $i < $hotelCount; $i++) {
                    $hotel = $hotels->random();
                    $checkIn = $arrivalDate->copy()->addDays($i * 2);
                    $checkOut = $checkIn->copy()->addDays(rand(1, 3));
                    
                    if ($checkOut->lte($departureDate)) {
                        $inquiryResources[] = [
                            'inquiry_id' => $inquiry->id,
                            'resource_type' => 'hotel',
                            'resource_id' => $hotel->id,
                            'resource_name' => $hotel->name,
                            'start_date' => $checkIn,
                            'end_date' => $checkOut,
                            'quantity' => rand(1, 2), // 1-2 rooms
                            'unit_price' => $hotel->price_per_night,
                            'total_price' => $hotel->price_per_night * rand(1, 2) * $checkIn->diffInDays($checkOut),
                            'currency' => $hotel->currency,
                            'status' => $this->getInquiryResourceStatus($inquiry->status),
                            'notes' => "Hotel accommodation for {$numberPax} guests. Star rating: {$hotel->star_rating}",
                            'special_requests' => $this->generateHotelRequests(),
                            'availability_confirmed' => rand(0, 1),
                            'confirmation_number' => 'HOTEL-' . strtoupper(uniqid()),
                            'cancellation_policy' => $hotel->cancellation_policy ?? 'Free cancellation up to 24 hours before check-in',
                        ];
                    }
                }
            }
            
            // Generate vehicle resources for inquiries
            if ($vehicles->isNotEmpty()) {
                $vehicleCount = rand(1, 2); // 1-2 vehicles per inquiry
                for ($i = 0; $i < $vehicleCount; $i++) {
                    $vehicle = $vehicles->random();
                    $startDate = $arrivalDate->copy()->addDays($i);
                    $endDate = $startDate->copy()->addDays(rand(1, 5));
                    
                    if ($endDate->lte($departureDate)) {
                        $days = $startDate->diffInDays($endDate);
                        $pricePerDay = $vehicle->price_per_day;
                        $totalPrice = $pricePerDay * $days;
                        
                        $inquiryResources[] = [
                            'inquiry_id' => $inquiry->id,
                            'resource_type' => 'vehicle',
                            'resource_id' => $vehicle->id,
                            'resource_name' => $vehicle->name,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'quantity' => 1,
                            'unit_price' => $pricePerDay,
                            'total_price' => $totalPrice,
                            'currency' => $vehicle->currency,
                            'status' => $this->getInquiryResourceStatus($inquiry->status),
                            'notes' => "Vehicle: {$vehicle->name} ({$vehicle->type}). Capacity: {$vehicle->capacity} passengers. Driver: {$vehicle->driver_name}",
                            'special_requests' => $this->generateVehicleRequests(),
                            'availability_confirmed' => rand(0, 1),
                            'confirmation_number' => 'VEHICLE-' . strtoupper(uniqid()),
                            'cancellation_policy' => 'Free cancellation up to 48 hours before rental start',
                        ];
                    }
                }
            }
            
            // Generate guide resources for inquiries
            if ($guides->isNotEmpty()) {
                $guideCount = rand(1, 2); // 1-2 guides per inquiry
                for ($i = 0; $i < $guideCount; $i++) {
                    $guide = $guides->random();
                    $startDate = $arrivalDate->copy()->addDays($i);
                    $endDate = $startDate->copy()->addDays(rand(1, 3));
                    
                    if ($endDate->lte($departureDate)) {
                        $days = $startDate->diffInDays($endDate);
                        $pricePerDay = $guide->price_per_day;
                        $totalPrice = $pricePerDay * $days;
                        
                        $inquiryResources[] = [
                            'inquiry_id' => $inquiry->id,
                            'resource_type' => 'guide',
                            'resource_id' => $guide->id,
                            'resource_name' => $guide->name,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'quantity' => 1,
                            'unit_price' => $pricePerDay,
                            'total_price' => $totalPrice,
                            'currency' => $guide->currency,
                            'status' => $this->getInquiryResourceStatus($inquiry->status),
                            'notes' => "Professional guide: {$guide->name}. Languages: " . implode(', ', $guide->languages) . ". Specializations: " . implode(', ', $guide->specializations),
                            'special_requests' => $this->generateGuideRequests(),
                            'availability_confirmed' => rand(0, 1),
                            'confirmation_number' => 'GUIDE-' . strtoupper(uniqid()),
                            'cancellation_policy' => 'Free cancellation up to 24 hours before tour start',
                        ];
                    }
                }
            }
            
            // Generate representative resources for inquiries
            if ($representatives->isNotEmpty()) {
                $repCount = rand(0, 1); // 0-1 representatives per inquiry
                if ($repCount > 0) {
                    $representative = $representatives->random();
                    $startDate = $arrivalDate;
                    $endDate = $departureDate;
                    
                    $days = $startDate->diffInDays($endDate);
                    $pricePerDay = $representative->price_per_day;
                    $totalPrice = $pricePerDay * $days;
                    
                    $inquiryResources[] = [
                        'inquiry_id' => $inquiry->id,
                        'resource_type' => 'representative',
                        'resource_id' => $representative->id,
                        'resource_name' => $representative->name,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'quantity' => 1,
                        'unit_price' => $pricePerDay,
                        'total_price' => $totalPrice,
                        'currency' => $representative->currency,
                        'status' => $this->getInquiryResourceStatus($inquiry->status),
                        'notes' => "Tour representative: {$representative->name}. Company: {$representative->company_name}. Service areas: " . implode(', ', $representative->service_areas),
                        'special_requests' => $this->generateRepresentativeRequests(),
                        'availability_confirmed' => rand(0, 1),
                        'confirmation_number' => 'REP-' . strtoupper(uniqid()),
                        'cancellation_policy' => 'Free cancellation up to 48 hours before service start',
                    ];
                }
            }
        }
        
        // Add some additional inquiry resources for different scenarios
        $additionalResources = [
            [
                'inquiry_id' => $inquiries->random()->id,
                'resource_type' => 'hotel',
                'resource_id' => $hotels->random()->id,
                'resource_name' => 'Luxury Nile View Hotel',
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(18),
                'quantity' => 1,
                'unit_price' => 350.00,
                'total_price' => 1050.00,
                'currency' => 'USD',
                'status' => ResourceStatus::AVAILABLE,
                'notes' => 'Luxury hotel with Nile view and premium amenities',
                'special_requests' => ['Nile view room', 'Late checkout', 'Airport transfer', 'Room service'],
                'availability_confirmed' => 1,
                'confirmation_number' => 'LUXURY-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 72 hours before check-in',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'resource_type' => 'vehicle',
                'resource_id' => $vehicles->random()->id,
                'resource_name' => 'VIP Airport Transfer',
                'start_date' => now()->addDays(8),
                'end_date' => now()->addDays(8),
                'quantity' => 1,
                'unit_price' => 120.00,
                'total_price' => 120.00,
                'currency' => 'USD',
                'status' => ResourceStatus::AVAILABLE,
                'notes' => 'VIP airport transfer service with luxury vehicle and meet & greet',
                'special_requests' => ['Meet and greet', 'Bottled water', 'WiFi', 'Airport assistance'],
                'availability_confirmed' => 1,
                'confirmation_number' => 'VIP-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 24 hours before service',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'resource_type' => 'guide',
                'resource_id' => $guides->random()->id,
                'resource_name' => 'Expert Egyptologist Guide',
                'start_date' => now()->addDays(25),
                'end_date' => now()->addDays(27),
                'quantity' => 1,
                'unit_price' => 250.00,
                'total_price' => 750.00,
                'currency' => 'USD',
                'status' => ResourceStatus::AVAILABLE,
                'notes' => 'Expert Egyptologist with PhD and extensive archaeological knowledge',
                'special_requests' => ['Early morning start', 'Photography permission', 'Academic materials', 'Private tour'],
                'availability_confirmed' => 1,
                'confirmation_number' => 'EXPERT-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 48 hours before tour',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'resource_type' => 'representative',
                'resource_id' => $representatives->random()->id,
                'resource_name' => 'Full Service Representative',
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(47),
                'quantity' => 1,
                'unit_price' => 80.00,
                'total_price' => 560.00,
                'currency' => 'USD',
                'status' => ResourceStatus::AVAILABLE,
                'notes' => 'Full service representative providing 24/7 assistance throughout the tour',
                'special_requests' => ['24/7 availability', 'Multilingual support', 'Emergency assistance', 'Local recommendations'],
                'availability_confirmed' => 1,
                'confirmation_number' => 'FULL-' . strtoupper(uniqid()),
                'cancellation_policy' => 'Free cancellation up to 72 hours before service',
            ]
        ];
        
        $allInquiryResources = array_merge($inquiryResources, $additionalResources);
        
        foreach ($allInquiryResources as $resourceData) {
            InquiryResource::create($resourceData);
        }

        $this->command->info('Inquiry resources seeded successfully!');
    }
    
    private function getInquiryResourceStatus($inquiryStatus): ResourceStatus
    {
        switch ($inquiryStatus) {
            case 'pending':
                return ResourceStatus::AVAILABLE;
            case 'confirmed':
                return ResourceStatus::AVAILABLE;
            case 'cancelled':
                return ResourceStatus::OUT_OF_SERVICE;
            default:
                return ResourceStatus::AVAILABLE;
        }
    }
    
    private function generateHotelRequests(): array
    {
        $requests = [
            'High floor room',
            'Ocean view',
            'City view',
            'Late checkout',
            'Early check-in',
            'Airport transfer',
            'Room service',
            'WiFi access',
            'Extra bed',
            'Non-smoking room',
            'Balcony',
            'Mini bar',
            'Safe deposit box',
            'Daily housekeeping',
            'Concierge service'
        ];
        
        return array_rand(array_flip($requests), rand(1, 4));
    }
    
    private function generateVehicleRequests(): array
    {
        $requests = [
            'Air conditioning',
            'WiFi',
            'Child seat',
            'GPS navigation',
            'Airport pickup',
            'Meet and greet',
            'Bottled water',
            'Extra luggage space',
            'Driver speaks English',
            'Fuel included',
            'Insurance coverage',
            'Emergency contact',
            'Route planning',
            'Multiple stops',
            'Flexible timing'
        ];
        
        return array_rand(array_flip($requests), rand(1, 4));
    }
    
    private function generateGuideRequests(): array
    {
        $requests = [
            'Early morning start',
            'Photography permission',
            'Academic materials',
            'Multilingual guide',
            'Private tour',
            'Custom itinerary',
            'Transportation included',
            'Meals included',
            'Entrance fees included',
            'Expert knowledge',
            'Historical context',
            'Cultural insights',
            'Flexible schedule',
            'Group size limit',
            'Specialized focus'
        ];
        
        return array_rand(array_flip($requests), rand(1, 4));
    }
    
    private function generateRepresentativeRequests(): array
    {
        $requests = [
            '24/7 availability',
            'Multilingual support',
            'Emergency assistance',
            'Airport assistance',
            'Hotel check-in help',
            'Restaurant reservations',
            'Shopping assistance',
            'Cultural insights',
            'Local recommendations',
            'Translation services',
            'Document assistance',
            'Transportation coordination',
            'Activity booking',
            'Problem resolution',
            'Follow-up service'
        ];
        
        return array_rand(array_flip($requests), rand(1, 4));
    }
}
