<?php

namespace App\Observers;

use App\Models\SupplierHotel;

class SupplierHotelObserver
{
    /**
     * Handle the SupplierHotel "created" event.
     */
    public function created(SupplierHotel $supplierHotel): void
    {
        $supplierHotel->autoTranslate();
    }

    /**
     * Handle the SupplierHotel "updated" event.
     */
    public function updated(SupplierHotel $supplierHotel): void
    {
        $supplierHotel->autoTranslate();
    }

    /**
     * Handle the SupplierHotel "deleted" event.
     */
    public function deleted(SupplierHotel $supplierHotel): void
    {
        //
    }

    /**
     * Handle the SupplierHotel "restored" event.
     */
    public function restored(SupplierHotel $supplierHotel): void
    {
        //
    }

    /**
     * Handle the SupplierHotel "force deleted" event.
     */
    public function forceDeleted(SupplierHotel $supplierHotel): void
    {
        //
    }
}



