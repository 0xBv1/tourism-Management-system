<?php

namespace Database\Seeders;

use App\Models\BookingFile;
use App\Models\Inquiry;
use App\Enums\BookingStatus;
use Illuminate\Database\Seeder;

class BookingFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inquiries = Inquiry::whereIn('status', ['confirmed', 'in_progress'])->get();
        
        if ($inquiries->isEmpty()) {
            $this->command->warn('No confirmed or in-progress inquiries found. Please run InquirySeeder first.');
            return;
        }
        
        $bookingFiles = [];
        
        foreach ($inquiries as $inquiry) {
            $status = $inquiry->status === 'confirmed' ? BookingStatus::CONFIRMED : BookingStatus::IN_PROGRESS;
            $generatedAt = $inquiry->confirmed_at ?? now()->subDays(rand(1, 30));
            $sentAt = $status === BookingStatus::CONFIRMED ? $generatedAt->copy()->addHours(rand(1, 24)) : null;
            $downloadedAt = $sentAt ? $sentAt->copy()->addDays(rand(1, 7)) : null;
            
            $bookingFiles[] = [
                'inquiry_id' => $inquiry->id,
                'file_name' => 'Booking_' . $inquiry->inquiry_id . '_' . now()->format('Y-m-d') . '.pdf',
                'file_path' => 'booking-files/' . $inquiry->inquiry_id . '/booking_file.pdf',
                'status' => $status,
                'generated_at' => $generatedAt,
                'sent_at' => $sentAt,
                'downloaded_at' => $downloadedAt,
                'checklist' => [
                    'hotel_reservations' => rand(0, 1),
                    'transportation_arranged' => rand(0, 1),
                    'guide_assigned' => rand(0, 1),
                    'meals_included' => rand(0, 1),
                    'entrance_tickets' => rand(0, 1),
                    'insurance_coverage' => rand(0, 1),
                    'emergency_contacts' => rand(0, 1),
                    'itinerary_confirmed' => rand(0, 1),
                    'payment_schedule' => rand(0, 1),
                    'special_requests' => rand(0, 1)
                ],
                'notes' => $this->generateBookingNotes($inquiry->tour_name, $status),
                'total_amount' => $inquiry->total_amount,
                'currency' => 'USD',
            ];
        }
        
        // Add some additional booking files for different statuses
        $additionalBookings = [
            [
                'inquiry_id' => $inquiries->random()->id,
                'file_name' => 'Booking_PENDING_' . now()->format('Y-m-d') . '.pdf',
                'file_path' => 'booking-files/pending/booking_file.pdf',
                'status' => BookingStatus::PENDING,
                'generated_at' => now()->subDays(5),
                'sent_at' => null,
                'downloaded_at' => null,
                'checklist' => [
                    'hotel_reservations' => 0,
                    'transportation_arranged' => 0,
                    'guide_assigned' => 0,
                    'meals_included' => 0,
                    'entrance_tickets' => 0,
                    'insurance_coverage' => 0,
                    'emergency_contacts' => 0,
                    'itinerary_confirmed' => 0,
                    'payment_schedule' => 0,
                    'special_requests' => 0
                ],
                'notes' => 'Booking file is being prepared. Awaiting final confirmation from client.',
                'total_amount' => 1500.00,
                'currency' => 'USD',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'file_name' => 'Booking_COMPLETED_' . now()->subDays(10)->format('Y-m-d') . '.pdf',
                'file_path' => 'booking-files/completed/booking_file.pdf',
                'status' => BookingStatus::COMPLETED,
                'generated_at' => now()->subDays(15),
                'sent_at' => now()->subDays(14),
                'downloaded_at' => now()->subDays(12),
                'checklist' => [
                    'hotel_reservations' => 1,
                    'transportation_arranged' => 1,
                    'guide_assigned' => 1,
                    'meals_included' => 1,
                    'entrance_tickets' => 1,
                    'insurance_coverage' => 1,
                    'emergency_contacts' => 1,
                    'itinerary_confirmed' => 1,
                    'payment_schedule' => 1,
                    'special_requests' => 1
                ],
                'notes' => 'Tour completed successfully. All services provided as per itinerary. Client satisfied with the experience.',
                'total_amount' => 2200.00,
                'currency' => 'USD',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'file_name' => 'Booking_CANCELLED_' . now()->subDays(20)->format('Y-m-d') . '.pdf',
                'file_path' => 'booking-files/cancelled/booking_file.pdf',
                'status' => BookingStatus::CANCELLED,
                'generated_at' => now()->subDays(25),
                'sent_at' => now()->subDays(24),
                'downloaded_at' => now()->subDays(22),
                'checklist' => [
                    'hotel_reservations' => 1,
                    'transportation_arranged' => 1,
                    'guide_assigned' => 1,
                    'meals_included' => 0,
                    'entrance_tickets' => 0,
                    'insurance_coverage' => 1,
                    'emergency_contacts' => 1,
                    'itinerary_confirmed' => 0,
                    'payment_schedule' => 0,
                    'special_requests' => 0
                ],
                'notes' => 'Booking cancelled by client due to personal reasons. Refund processed according to cancellation policy.',
                'total_amount' => 1800.00,
                'currency' => 'USD',
            ],
            [
                'inquiry_id' => $inquiries->random()->id,
                'file_name' => 'Booking_REFUNDED_' . now()->subDays(30)->format('Y-m-d') . '.pdf',
                'file_path' => 'booking-files/refunded/booking_file.pdf',
                'status' => BookingStatus::REFUNDED,
                'generated_at' => now()->subDays(35),
                'sent_at' => now()->subDays(34),
                'downloaded_at' => now()->subDays(32),
                'checklist' => [
                    'hotel_reservations' => 1,
                    'transportation_arranged' => 1,
                    'guide_assigned' => 1,
                    'meals_included' => 1,
                    'entrance_tickets' => 1,
                    'insurance_coverage' => 1,
                    'emergency_contacts' => 1,
                    'itinerary_confirmed' => 1,
                    'payment_schedule' => 1,
                    'special_requests' => 1
                ],
                'notes' => 'Tour was completed but client requested refund due to service quality issues. Full refund processed after investigation.',
                'total_amount' => 2000.00,
                'currency' => 'USD',
            ]
        ];
        
        $allBookingFiles = array_merge($bookingFiles, $additionalBookings);
        
        foreach ($allBookingFiles as $bookingFileData) {
            // Check if booking file already exists for this inquiry
            $existingBookingFile = BookingFile::where('inquiry_id', $bookingFileData['inquiry_id'])->first();
            
            if (!$existingBookingFile) {
                BookingFile::create($bookingFileData);
            }
        }

        $this->command->info('Booking files seeded successfully!');
    }
    
    private function generateBookingNotes(string $tourName, BookingStatus $status): string
    {
        $notes = [];
        
        if (str_contains(strtolower($tourName), 'cairo')) {
            $notes[] = 'Cairo attractions included: Giza Pyramids, Egyptian Museum, Islamic Cairo';
        }
        
        if (str_contains(strtolower($tourName), 'luxor')) {
            $notes[] = 'Luxor sites covered: Valley of the Kings, Karnak Temple, Luxor Temple';
        }
        
        if (str_contains(strtolower($tourName), 'aswan')) {
            $notes[] = 'Aswan highlights: Philae Temple, Abu Simbel, Nubian culture';
        }
        
        if (str_contains(strtolower($tourName), 'hurghada') || str_contains(strtolower($tourName), 'red sea')) {
            $notes[] = 'Red Sea activities: Diving, snorkeling, desert safari';
        }
        
        if (str_contains(strtolower($tourName), 'sharm')) {
            $notes[] = 'Sharm El Sheikh: Diving sites, Mount Sinai, marine activities';
        }
        
        if (str_contains(strtolower($tourName), 'alexandria')) {
            $notes[] = 'Alexandria attractions: Library, Corniche, historical sites';
        }
        
        if (str_contains(strtolower($tourName), 'siwa')) {
            $notes[] = 'Siwa Oasis: Desert experience, Berber culture, oasis activities';
        }
        
        if (str_contains(strtolower($tourName), 'dahab')) {
            $notes[] = 'Dahab activities: Blue Hole diving, desert adventures, Bedouin experiences';
        }
        
        if (str_contains(strtolower($tourName), 'marsa alam')) {
            $notes[] = 'Marsa Alam: Coral reefs, marine life, diving experiences';
        }
        
        if (str_contains(strtolower($tourName), 'el gouna')) {
            $notes[] = 'El Gouna: Water sports, lagoons, beach activities';
        }
        
        // Add status-specific notes
        switch ($status) {
            case BookingStatus::PENDING:
                $notes[] = 'Booking confirmation pending. Awaiting client response.';
                break;
            case BookingStatus::CONFIRMED:
                $notes[] = 'Booking confirmed. All arrangements in progress.';
                break;
            case BookingStatus::IN_PROGRESS:
                $notes[] = 'Tour in progress. All services being provided as scheduled.';
                break;
            case BookingStatus::COMPLETED:
                $notes[] = 'Tour completed successfully. All services provided as per itinerary.';
                break;
            case BookingStatus::CANCELLED:
                $notes[] = 'Booking cancelled. Refund processed according to policy.';
                break;
            case BookingStatus::REFUNDED:
                $notes[] = 'Refund processed. Booking closed.';
                break;
        }
        
        return implode('. ', $notes) . '.';
    }
}