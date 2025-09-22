<?php

namespace App\Console\Commands;

use App\Models\BookingFile;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\ResourceBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixBookingFileDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:fix-data {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix booking file data inconsistencies and relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in dry-run mode. No changes will be made.');
        }

        $this->info('Starting booking file data fix...');

        // Fix 1: Update booking file status enum values
        $this->fixBookingFileStatuses($dryRun);

        // Fix 2: Sync total amounts from resource bookings
        $this->syncTotalAmounts($dryRun);

        // Fix 3: Sync payment data
        $this->syncPaymentData($dryRun);

        // Fix 4: Update inquiry booking file relationships
        $this->updateInquiryRelationships($dryRun);

        // Fix 5: Clean up orphaned records
        $this->cleanupOrphanedRecords($dryRun);

        $this->info('Booking file data fix completed!');
    }

    private function fixBookingFileStatuses(bool $dryRun): void
    {
        $this->info('Fixing booking file status enum values...');

        $statusMapping = [
            'generated' => 'pending',
            'sent' => 'confirmed',
            'downloaded' => 'completed',
        ];

        foreach ($statusMapping as $oldStatus => $newStatus) {
            $count = BookingFile::where('status', $oldStatus)->count();
            
            if ($count > 0) {
                $this->line("Found {$count} booking files with status '{$oldStatus}'");
                
                if (!$dryRun) {
                    BookingFile::where('status', $oldStatus)->update(['status' => $newStatus]);
                    $this->info("Updated {$count} booking files to status '{$newStatus}'");
                } else {
                    $this->info("Would update {$count} booking files to status '{$newStatus}'");
                }
            }
        }
    }

    private function syncTotalAmounts(bool $dryRun): void
    {
        $this->info('Syncing total amounts from resource bookings...');

        $bookingFiles = BookingFile::with('resourceBookings')->get();
        $updated = 0;

        foreach ($bookingFiles as $bookingFile) {
            $calculatedTotal = $bookingFile->calculateTotalFromResourceBookings();
            
            if ($calculatedTotal > 0 && $bookingFile->total_amount != $calculatedTotal) {
                $this->line("Booking file {$bookingFile->id}: {$bookingFile->total_amount} -> {$calculatedTotal}");
                
                if (!$dryRun) {
                    $bookingFile->update(['total_amount' => $calculatedTotal]);
                }
                
                $updated++;
            }
        }

        if ($dryRun) {
            $this->info("Would update {$updated} booking file total amounts");
        } else {
            $this->info("Updated {$updated} booking file total amounts");
        }
    }

    private function syncPaymentData(bool $dryRun): void
    {
        $this->info('Syncing payment data...');

        $bookingFiles = BookingFile::with('payments', 'inquiry')->get();
        $updated = 0;

        foreach ($bookingFiles as $bookingFile) {
            $oldPaidAmount = $bookingFile->inquiry?->paid_amount ?? 0;
            $oldRemainingAmount = $bookingFile->inquiry?->remaining_amount ?? 0;
            
            if (!$dryRun) {
                $bookingFile->syncPaymentData();
            }
            
            $newPaidAmount = $bookingFile->total_paid;
            $newRemainingAmount = $bookingFile->remaining_amount;
            
            if ($oldPaidAmount != $newPaidAmount || $oldRemainingAmount != $newRemainingAmount) {
                $this->line("Booking file {$bookingFile->id}: Paid {$oldPaidAmount} -> {$newPaidAmount}, Remaining {$oldRemainingAmount} -> {$newRemainingAmount}");
                $updated++;
            }
        }

        if ($dryRun) {
            $this->info("Would update {$updated} payment data records");
        } else {
            $this->info("Updated {$updated} payment data records");
        }
    }

    private function updateInquiryRelationships(bool $dryRun): void
    {
        $this->info('Updating inquiry relationships...');

        $inquiries = Inquiry::whereNotNull('booking_file_id')->get();
        $updated = 0;

        foreach ($inquiries as $inquiry) {
            if (!$dryRun) {
                $inquiry->syncBookingFileData();
            }
            $updated++;
        }

        if ($dryRun) {
            $this->info("Would update {$updated} inquiry relationships");
        } else {
            $this->info("Updated {$updated} inquiry relationships");
        }
    }

    private function cleanupOrphanedRecords(bool $dryRun): void
    {
        $this->info('Cleaning up orphaned records...');

        // Find orphaned resource bookings
        $orphanedResourceBookings = ResourceBooking::whereDoesntHave('bookingFile')->count();
        if ($orphanedResourceBookings > 0) {
            $this->warn("Found {$orphanedResourceBookings} orphaned resource bookings");
            if (!$dryRun) {
                ResourceBooking::whereDoesntHave('bookingFile')->delete();
                $this->info("Deleted {$orphanedResourceBookings} orphaned resource bookings");
            }
        }

        // Find orphaned payments
        $orphanedPayments = Payment::whereDoesntHave('booking')->count();
        if ($orphanedPayments > 0) {
            $this->warn("Found {$orphanedPayments} orphaned payments");
            if (!$dryRun) {
                Payment::whereDoesntHave('booking')->delete();
                $this->info("Deleted {$orphanedPayments} orphaned payments");
            }
        }

        // Find booking files without inquiries
        $orphanedBookingFiles = BookingFile::whereDoesntHave('inquiry')->count();
        if ($orphanedBookingFiles > 0) {
            $this->warn("Found {$orphanedBookingFiles} booking files without inquiries");
            if (!$dryRun) {
                BookingFile::whereDoesntHave('inquiry')->delete();
                $this->info("Deleted {$orphanedBookingFiles} orphaned booking files");
            }
        }
    }
}
