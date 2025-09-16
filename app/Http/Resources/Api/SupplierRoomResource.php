<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'banner' => $this->banner ? asset('storage/' . $this->banner) : null,
            'gallery' => $this->gallery ? array_map(function($image) {
                return asset('storage/' . $image);
            }, $this->gallery) : [],
            'enabled' => $this->enabled,
            'bed_count' => $this->bed_count,
            'room_type' => $this->room_type,
            'max_capacity' => $this->max_capacity,
            'bed_types' => $this->bed_types,
            'night_price' => $this->night_price,
            'extra_bed_available' => $this->extra_bed_available,
            'extra_bed_price' => $this->extra_bed_price,
            'max_extra_beds' => $this->max_extra_beds,
            'extra_bed_description' => $this->extra_bed_description,
            'approved' => $this->approved,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'hotel' => [
                'id' => $this->supplierHotel->id ?? null,
                'name' => $this->supplierHotel->name ?? null,
                'slug' => $this->supplierHotel->slug ?? null,
            ],
            'amenities' => $this->amenities->map(function($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'icon' => $amenity->icon ?? null,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
