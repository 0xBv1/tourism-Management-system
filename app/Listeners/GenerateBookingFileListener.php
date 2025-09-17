<?php

namespace App\Listeners;

use App\Events\InquiryConfirmed;
use App\Models\BookingFile;
use App\Models\User;
use App\Notifications\Admin\InquiryConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class GenerateBookingFileListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\InquiryConfirmed  $event
     * @return void
     */
    public function handle(InquiryConfirmed $event)
    {
        $inquiry = $event->inquiry;
        
        // Generate booking file content
        $bookingContent = $this->generateBookingFileContent($inquiry);
        
        // Create booking file record
        $bookingFile = BookingFile::create([
            'inquiry_id' => $inquiry->id,
            'file_name' => 'booking_' . $inquiry->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf',
            'file_path' => 'booking-files/booking_' . $inquiry->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf',
            'status' => 'generated',
            'generated_at' => now(),
        ]);
        
        // Store the file
        Storage::disk('public')->put($bookingFile->file_path, $bookingContent);
        
        // Update inquiry with booking file reference
        $inquiry->update(['booking_file_id' => $bookingFile->id]);
        
        // Send notification to admin users
        $adminUsers = User::role('admin')->get();
        if ($adminUsers->isNotEmpty()) {
            Notification::send($adminUsers, new InquiryConfirmedNotification($inquiry));
        }
    }
    
    /**
     * Generate booking file content
     *
     * @param \App\Models\Inquiry $inquiry
     * @return string
     */
    private function generateBookingFileContent($inquiry): string
    {
        $content = "BOOKING CONFIRMATION\n";
        $content .= "==================\n\n";
        $content .= "Booking ID: #{$inquiry->id}\n";
        $content .= "Date: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $content .= "Customer Information:\n";
        $content .= "Name: {$inquiry->name}\n";
        $content .= "Email: {$inquiry->email}\n";
        $content .= "Phone: {$inquiry->phone}\n\n";
        $content .= "Inquiry Details:\n";
        $content .= "Subject: {$inquiry->subject}\n";
        $content .= "Message: {$inquiry->message}\n\n";
        $content .= "Status: Confirmed\n";
        $content .= "Confirmed At: " . $inquiry->confirmed_at->format('Y-m-d H:i:s') . "\n\n";
        
        if ($inquiry->assignedUser) {
            $content .= "Assigned To: {$inquiry->assignedUser->name}\n";
        }
        
        if ($inquiry->admin_notes) {
            $content .= "Admin Notes: {$inquiry->admin_notes}\n";
        }
        
        $content .= "\n\nThis booking has been confirmed and is ready for processing.\n";
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n";
        
        return $content;
    }
}
