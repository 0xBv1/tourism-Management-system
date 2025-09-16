<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceApproval;
use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\User;

class ServiceApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some suppliers
        $suppliers = Supplier::take(3)->get();
        
        if ($suppliers->isEmpty()) {
            return;
        }

        // Get admin user
        $admin = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Administrator', 'Admin']);
        })->first();

        if (!$admin) {
            return;
        }

        foreach ($suppliers as $supplier) {
            // Create pending hotel approval
            if ($supplier->hotels()->count() > 0) {
                $hotel = $supplier->hotels()->first();
                ServiceApproval::create([
                    'supplier_id' => $supplier->id,
                    'service_type' => 'hotel',
                    'service_id' => $hotel->id,
                    'status' => 'pending',
                ]);
            }

            // Create approved tour approval
            if ($supplier->tours()->count() > 0) {
                $tour = $supplier->tours()->first();
                ServiceApproval::create([
                    'supplier_id' => $supplier->id,
                    'service_type' => 'tour',
                    'service_id' => $tour->id,
                    'status' => 'approved',
                    'approved_by' => $admin->id,
                    'approved_at' => now()->subDays(2),
                ]);
            }

            // Create rejected trip approval
            if ($supplier->trips()->count() > 0) {
                $trip = $supplier->trips()->first();
                ServiceApproval::create([
                    'supplier_id' => $supplier->id,
                    'service_type' => 'trip',
                    'service_id' => $trip->id,
                    'status' => 'rejected',
                    'approved_by' => $admin->id,
                    'rejected_at' => now()->subDays(1),
                    'rejection_reason' => 'Service details are incomplete. Please provide more information about the trip itinerary and pricing.',
                ]);
            }

            // Create pending transport approval
            if ($supplier->transports()->count() > 0) {
                $transport = $supplier->transports()->first();
                ServiceApproval::create([
                    'supplier_id' => $supplier->id,
                    'service_type' => 'transport',
                    'service_id' => $transport->id,
                    'status' => 'pending',
                ]);
            }
        }


    }
}
