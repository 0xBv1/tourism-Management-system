<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplierRoom;
use App\Models\ServiceApproval;

class CreateRoomServiceApprovalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all existing rooms
        $rooms = SupplierRoom::all();
        
        // Get existing service approval IDs for rooms
        $existingApprovalIds = ServiceApproval::where('service_type', 'room')
            ->pluck('service_id')
            ->toArray();
        
        // Filter rooms that don't have service approval records
        $roomsWithoutApprovals = $rooms->filter(function($room) use ($existingApprovalIds) {
            return !in_array($room->id, $existingApprovalIds);
        });

        foreach ($roomsWithoutApprovals as $room) {
            // Create service approval record for each room
            ServiceApproval::create([
                'supplier_id' => $room->supplierHotel->supplier_id,
                'service_type' => 'room',
                'service_id' => $room->id,
                'status' => $room->approved ? 'approved' : 'pending',
                'approved_by' => null, // Will be set when admin approves
                'approved_at' => $room->approved ? now() : null,
            ]);
        }

        $this->command->info("Created service approval records for {$roomsWithoutApprovals->count()} rooms.");
    }
}
