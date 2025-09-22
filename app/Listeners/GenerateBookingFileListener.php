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
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        // Generate PDF content
        $pdfContent = $this->generateBookingFilePDF($inquiry);
        
        // Create booking file record
        $bookingFile = BookingFile::create([
            'inquiry_id' => $inquiry->id,
            'file_name' => 'booking_' . $inquiry->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf',
            'file_path' => 'booking-files/booking_' . $inquiry->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf',
            'status' => 'pending',
            'generated_at' => now(),
        ]);
        
        // Store the PDF file
        Storage::disk('public')->put($bookingFile->file_path, $pdfContent);
        
        // Update inquiry with booking file reference
        $inquiry->update(['booking_file_id' => $bookingFile->id]);
        
        // Send notification to admin users
        $adminUsers = User::role('admin')->get();
        if ($adminUsers->isNotEmpty()) {
            Notification::send($adminUsers, new InquiryConfirmedNotification($inquiry));
        }
    }
    
    /**
     * Generate booking file PDF
     *
     * @param \App\Models\Inquiry $inquiry
     * @return string
     */
    private function generateBookingFilePDF($inquiry): string
    {
        $data = [
            'inquiry' => $inquiry,
            'generated_at' => now(),
            'booking_id' => $inquiry->id,
        ];

        $pdf = Pdf::loadView('emails.booking-confirmation-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
        ]);

        return $pdf->output();
    }
}
