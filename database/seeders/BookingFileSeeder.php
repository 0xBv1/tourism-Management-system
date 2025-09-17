<?php

namespace Database\Seeders;

use App\Models\BookingFile;
use App\Models\Inquiry;
use App\Enums\BookingStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some existing inquiries
        $inquiries = Inquiry::with('client')->take(5)->get();
        
        if ($inquiries->isEmpty()) {
            $this->command->info('No inquiries found. Please run InquirySeeder first.');
            return;
        }

        $sampleBookings = [
            [
                'inquiry_id' => $inquiries->first()->id,
                'file_name' => 'booking_confirmation_001.pdf',
                'file_path' => '/storage/bookings/booking_confirmation_001.pdf',
                'status' => BookingStatus::CONFIRMED,
                'total_amount' => 1500.00,
                'currency' => 'USD',
                'notes' => 'Standard booking package with accommodation and tours.',
                'checklist' => [
                    'accommodation_booked' => true,
                    'tours_scheduled' => true,
                    'transportation_arranged' => false,
                    'insurance_processed' => false,
                    'final_documents_sent' => false,
                ],
                'generated_at' => now()->subDays(2),
                'sent_at' => now()->subDays(1),
            ],
            [
                'inquiry_id' => $inquiries->skip(1)->first()->id ?? $inquiries->first()->id,
                'file_name' => 'booking_confirmation_002.pdf',
                'file_path' => '/storage/bookings/booking_confirmation_002.pdf',
                'status' => BookingStatus::PENDING,
                'total_amount' => 2200.50,
                'currency' => 'EUR',
                'notes' => 'Premium package with luxury accommodation.',
                'checklist' => [
                    'accommodation_booked' => false,
                    'tours_scheduled' => false,
                    'transportation_arranged' => false,
                    'insurance_processed' => false,
                    'final_documents_sent' => false,
                ],
                'generated_at' => now()->subHours(5),
            ],
            [
                'inquiry_id' => $inquiries->skip(2)->first()->id ?? $inquiries->first()->id,
                'file_name' => 'booking_confirmation_003.pdf',
                'file_path' => '/storage/bookings/booking_confirmation_003.pdf',
                'status' => BookingStatus::COMPLETED,
                'total_amount' => 800.75,
                'currency' => 'USD',
                'notes' => 'Budget package completed successfully.',
                'checklist' => [
                    'accommodation_booked' => true,
                    'tours_scheduled' => true,
                    'transportation_arranged' => true,
                    'insurance_processed' => true,
                    'final_documents_sent' => true,
                ],
                'generated_at' => now()->subDays(5),
                'sent_at' => now()->subDays(4),
                'downloaded_at' => now()->subDays(3),
            ],
        ];

        foreach ($sampleBookings as $bookingData) {
            BookingFile::create($bookingData);
        }

        $this->command->info('Sample booking files created successfully!');
    }
}
