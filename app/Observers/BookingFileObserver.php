<?php

namespace App\Observers;

use App\Models\BookingFile;
use Spatie\Activitylog\Models\Activity;

class BookingFileObserver
{
    /**
     * Handle the BookingFile "created" event.
     *
     * @param  \App\Models\BookingFile  $bookingFile
     * @return void
     */
    public function created(BookingFile $bookingFile)
    {
        activity()
            ->performedOn($bookingFile)
            ->withProperties([
                'inquiry_id' => $bookingFile->inquiry_id,
                'file_name' => $bookingFile->file_name,
                'status' => $bookingFile->status->value,
            ])
            ->log('Booking file created');
    }

    /**
     * Handle the BookingFile "updated" event.
     *
     * @param  \App\Models\BookingFile  $bookingFile
     * @return void
     */
    public function updated(BookingFile $bookingFile)
    {
        $changes = $bookingFile->getChanges();
        $original = $bookingFile->getOriginal();

        // Sync payment data when total_amount changes
        if (isset($changes['total_amount'])) {
            $bookingFile->syncPaymentData();
        }

        // Log status changes
        if (isset($changes['status'])) {
            $oldStatus = is_object($original['status']) ? $original['status']->value : $original['status'];
            $newStatus = is_object($changes['status']) ? $changes['status']->value : $changes['status'];
            
            activity()
                ->performedOn($bookingFile)
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'inquiry_id' => $bookingFile->inquiry_id,
                ])
                ->log('Booking file status changed from ' . $oldStatus . ' to ' . $newStatus);
        }

        // Log checklist updates
        if (isset($changes['checklist'])) {
            activity()
                ->performedOn($bookingFile)
                ->withProperties([
                    'checklist_changes' => $changes['checklist'],
                    'inquiry_id' => $bookingFile->inquiry_id,
                ])
                ->log('Booking file checklist updated');
        }

        // Log payment-related changes
        if (isset($changes['total_amount'])) {
            activity()
                ->performedOn($bookingFile)
                ->withProperties([
                    'old_amount' => $original['total_amount'],
                    'new_amount' => $changes['total_amount'],
                    'inquiry_id' => $bookingFile->inquiry_id,
                ])
                ->log('Booking file total amount updated');
        }

        // Log general updates (excluding timestamps)
        $excludeFields = ['updated_at', 'created_at'];
        $filteredChanges = array_diff_key($changes, array_flip($excludeFields));
        
        if (!empty($filteredChanges) && !isset($changes['status']) && !isset($changes['checklist']) && !isset($changes['total_amount'])) {
            activity()
                ->performedOn($bookingFile)
                ->withProperties([
                    'changes' => $filteredChanges,
                    'inquiry_id' => $bookingFile->inquiry_id,
                ])
                ->log('Booking file updated');
        }
    }

    /**
     * Handle the BookingFile "deleted" event.
     *
     * @param  \App\Models\BookingFile  $bookingFile
     * @return void
     */
    public function deleted(BookingFile $bookingFile)
    {
        activity()
            ->performedOn($bookingFile)
            ->withProperties([
                'inquiry_id' => $bookingFile->inquiry_id,
                'file_name' => $bookingFile->file_name,
                'status' => $bookingFile->status->value,
            ])
            ->log('Booking file deleted');
    }

    /**
     * Handle the BookingFile "restored" event.
     *
     * @param  \App\Models\BookingFile  $bookingFile
     * @return void
     */
    public function restored(BookingFile $bookingFile)
    {
        activity()
            ->performedOn($bookingFile)
            ->withProperties([
                'inquiry_id' => $bookingFile->inquiry_id,
                'file_name' => $bookingFile->file_name,
            ])
            ->log('Booking file restored');
    }

    /**
     * Handle the BookingFile "force deleted" event.
     *
     * @param  \App\Models\BookingFile  $bookingFile
     * @return void
     */
    public function forceDeleted(BookingFile $bookingFile)
    {
        activity()
            ->performedOn($bookingFile)
            ->withProperties([
                'inquiry_id' => $bookingFile->inquiry_id,
                'file_name' => $bookingFile->file_name,
            ])
            ->log('Booking file force deleted');
    }
}
