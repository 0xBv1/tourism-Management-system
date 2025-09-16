<?php

namespace App\Observers;

use App\Models\SupplierRoom;
use Illuminate\Support\Facades\Storage;

class SupplierRoomObserver
{
    /**
     * Handle the SupplierRoom "created" event.
     */
    public function created(SupplierRoom $supplierRoom): void
    {
        // Log room creation
        \Log::info('Supplier room created', [
            'room_id' => $supplierRoom->id,
            'hotel_id' => $supplierRoom->supplier_hotel_id,
            'name' => $supplierRoom->name
        ]);
    }

    /**
     * Handle the SupplierRoom "updated" event.
     */
    public function updated(SupplierRoom $supplierRoom): void
    {
        // Log room updates
        \Log::info('Supplier room updated', [
            'room_id' => $supplierRoom->id,
            'hotel_id' => $supplierRoom->supplier_hotel_id,
            'name' => $supplierRoom->name
        ]);
    }

    /**
     * Handle the SupplierRoom "deleted" event.
     */
    public function deleted(SupplierRoom $supplierRoom): void
    {
        // Clean up associated files
        if ($supplierRoom->featured_image) {
            Storage::disk('public')->delete($supplierRoom->featured_image);
        }
        
        if ($supplierRoom->banner) {
            Storage::disk('public')->delete($supplierRoom->banner);
        }
        
        if ($supplierRoom->gallery) {
            foreach ($supplierRoom->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Log room deletion
        \Log::info('Supplier room deleted', [
            'room_id' => $supplierRoom->id,
            'hotel_id' => $supplierRoom->supplier_hotel_id,
            'name' => $supplierRoom->name
        ]);
    }

    /**
     * Handle the SupplierRoom "restored" event.
     */
    public function restored(SupplierRoom $supplierRoom): void
    {
        // Log room restoration
        \Log::info('Supplier room restored', [
            'room_id' => $supplierRoom->id,
            'hotel_id' => $supplierRoom->supplier_hotel_id,
            'name' => $supplierRoom->name
        ]);
    }

    /**
     * Handle the SupplierRoom "force deleted" event.
     */
    public function forceDeleted(SupplierRoom $supplierRoom): void
    {
        // Log force deletion
        \Log::info('Supplier room force deleted', [
            'room_id' => $supplierRoom->id,
            'hotel_id' => $supplierRoom->supplier_hotel_id,
            'name' => $supplierRoom->name
        ]);
    }
}
