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
        
        // Generate filename with inquiry details
        $name = $this->sanitizeForFilename($inquiry->guest_name ?? 'Guest_Name');
        $nationality = $this->sanitizeForFilename($inquiry->nationality ?? 'Nationality');
        $filename = "booking-{$inquiry->id}-{$name}-{$nationality}.pdf";
        $filepath = "booking-files/{$filename}";
        
        // Create booking file record
        $bookingFile = BookingFile::create([
            'inquiry_id' => $inquiry->id,
            'file_name' => $filename,
            'file_path' => $filepath,
            'status' => 'pending',
            'generated_at' => now(),
        ]);
        
        // Store the PDF file
        Storage::disk('public')->put($bookingFile->file_path, $pdfContent);
        
        // Update inquiry with booking file reference
        $inquiry->update(['booking_file_id' => $bookingFile->id]);
        
        // Send notification to sales users and all users assigned to this inquiry
        $usersToNotify = collect();
        
        // Get all sales users
        $salesUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales', 'Reservation', 'Operator', 'Admin', 'Administrator']);
        })->get();
        $usersToNotify = $usersToNotify->merge($salesUsers);
        
        // Get all users assigned to this inquiry
        $assignedUsers = $inquiry->getAllAssignedUsers();
        foreach ($assignedUsers as $assignedUserData) {
            if (isset($assignedUserData['user']) && $assignedUserData['user']) {
                $usersToNotify->push($assignedUserData['user']);
            }
        }
        
        // Remove duplicates and send notifications
        $uniqueUsers = $usersToNotify->unique('id');
        if ($uniqueUsers->isNotEmpty()) {
            Notification::send($uniqueUsers, new InquiryConfirmedNotification($inquiry));
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
        // Load all necessary relationships for PDF generation
        $inquiry->load(['client', 'assignedUser.roles', 'assignedReservation.roles', 'assignedOperator.roles', 'assignedAdmin.roles', 'resources.addedBy', 'bookingFile.payments']);
        
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
    
    /**
     * Sanitize string for use in filename
     *
     * @param string $string
     * @return string
     */
    private function sanitizeForFilename(string $string): string
    {
        // Handle empty or whitespace-only strings
        if (empty(trim($string))) {
            return 'unknown';
        }
        
        // Remove special characters and replace spaces with hyphens
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $string);
        $sanitized = preg_replace('/\s+/', '-', trim($sanitized));
        $sanitized = strtolower($sanitized);
        
        // Limit length to avoid filesystem issues
        return substr($sanitized, 0, 50);
    }
}
