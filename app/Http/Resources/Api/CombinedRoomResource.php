<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CombinedRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->resource instanceof \App\Models\Room) {
            return $this->transformRegularRoom();
        } elseif ($this->resource instanceof \App\Models\SupplierRoom) {
            return $this->transformSupplierRoom();
        }

        return parent::toArray($request);
    }

    /**
     * Transform regular room data
     */
    protected function transformRegularRoom(): array
    {
        $room = $this->resource;

        $price = $room->price_per_night ?? $room->night_price ?? $room->price ?? 0;
        $currency = $room->currency ?? 'EGP';

        return [
            'id' => $room->id,
            'type' => 'regular',
            'name' => $room->name ?? null,
            'slug' => $room->slug ?? null,
            'bed_count' => $room->bed_count ?? null,
            'capacity' => $room->capacity ?? null,
            'night_price' => $price,
            'currency' => $currency,
            'enabled' => $room->enabled ?? true,
            'supplier' => null,
            'approval_status' => [
                'approved' => true,
                'enabled' => $room->enabled ?? true,
                'rejection_reason' => null,
                'status_label' => ($room->enabled ?? true) ? 'Active' : 'Inactive',
                'status_color' => ($room->enabled ?? true) ? 'success' : 'secondary',
            ],
            'commission' => [
                'rate' => 0,
                'rate_formatted' => '0%',
                'amount' => 0,
                'amount_formatted' => '0 ' . $currency,
            ],
            'recommendation_score' => 0,
            'created_at' => $room->created_at,
            'updated_at' => $room->updated_at,
        ];
    }

    /**
     * Transform supplier room data
     */
    protected function transformSupplierRoom(): array
    {
        $room = $this->resource;

        $price = $room->night_price ?? 0;
        $currency = 'EGP';
        $supplier = $room->supplierHotel ? $room->supplierHotel->supplier : null;
        $commissionRate = $supplier->commission_rate ?? 0;
        $commissionAmount = ($price * $commissionRate) / 100;

        return [
            'id' => $room->id,
            'type' => 'supplier',
            'name' => $room->name,
            'slug' => $room->slug,
            'bed_count' => $room->bed_count,
            'capacity' => $room->capacity ?? null,
            'night_price' => $price,
            'currency' => $currency,
            'enabled' => (bool) $room->enabled,
            'supplier' => $supplier ? [
                'id' => $supplier->id,
                'company_name' => $supplier->company_name,
                'company_email' => $supplier->company_email,
                'phone' => $supplier->phone,
                'website' => $supplier->website,
                'logo' => $supplier->logo,
                'banner' => $supplier->banner,
                'description' => $supplier->description,
                'commission_rate' => $commissionRate,
                'is_verified' => $supplier->is_verified ?? false,
                'is_active' => $supplier->is_active ?? true,
                'verified_at' => $supplier->verified_at,
            ] : null,
            'approval_status' => [
                'approved' => (bool) $room->approved,
                'enabled' => (bool) $room->enabled,
                'rejection_reason' => $room->rejection_reason ?? null,
                'status_label' => $this->getStatusLabel($room),
                'status_color' => $this->getStatusColor($room),
            ],
            'commission' => [
                'rate' => $commissionRate,
                'rate_formatted' => $commissionRate . '%',
                'amount' => $commissionAmount,
                'amount_formatted' => number_format($commissionAmount, 2) . ' ' . $currency,
            ],
            // For rooms we rely primarily on commission rate
            'recommendation_score' => round($commissionRate * 0.7, 2),
            'created_at' => $room->created_at,
            'updated_at' => $room->updated_at,
        ];
    }

    protected function getStatusLabel($room): string
    {
        if (!$room->approved) {
            return 'Pending Approval';
        }
        return $room->enabled ? 'Active' : 'Inactive';
    }

    protected function getStatusColor($room): string
    {
        if (!$room->approved) {
            return 'warning';
        }
        return $room->enabled ? 'success' : 'secondary';
    }
}


