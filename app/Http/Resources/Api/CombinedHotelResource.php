<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CombinedHotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if this is a regular hotel or supplier hotel
        if ($this->resource instanceof \App\Models\Hotel) {
            return $this->transformRegularHotel();
        } elseif ($this->resource instanceof \App\Models\SupplierHotel) {
            return $this->transformSupplierHotel();
        }

        return parent::toArray($request);
    }

    /**
     * Transform regular hotel data
     */
    protected function transformRegularHotel(): array
    {
        $hotel = $this->resource;
        
        return [
            'id' => $hotel->id,
            'type' => 'regular',
            'name' => $hotel->name,
            'description' => $hotel->description,
            'slug' => $hotel->slug,
            'stars' => $hotel->stars,
            'featured_image' => $hotel->featured_image,
            'banner' => $hotel->banner,
            'gallery' => $hotel->gallery ?? [],
            'address' => $hotel->address,
            'map_iframe' => $hotel->map_iframe,
            'phone_contact' => $hotel->phone_contact,
            'whatsapp_contact' => $hotel->whatsapp_contact,
            'enabled' => $hotel->enabled,
            'amenities' => $hotel->amenities ? $hotel->amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'icon' => $amenity->icon,
                ];
            }) : [],
            'rooms' => $hotel->rooms ? $hotel->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'price' => $room->price,
                    'capacity' => $room->capacity,
                ];
            }) : [],
            'supplier' => null, // Regular hotels don't have suppliers
            'approval_status' => [
                'approved' => true, // Regular hotels are always approved
                'enabled' => $hotel->enabled,
                'rejection_reason' => null,
                'status_label' => $hotel->enabled ? 'Active' : 'Inactive',
                'status_color' => $hotel->enabled ? 'success' : 'secondary',
            ],
            'commission' => [
                'rate' => 0,
                'rate_formatted' => '0%',
                'amount' => 0,
                'amount_formatted' => '0 EGP',
            ],
            'recommendation_score' => 0,
            'created_at' => $hotel->created_at,
            'updated_at' => $hotel->updated_at,
        ];
    }

    /**
     * Transform supplier hotel data
     */
    protected function transformSupplierHotel(): array
    {
        $hotel = $this->resource;
        
        return [
            'id' => $hotel->id,
            'type' => 'supplier',
            'name' => $hotel->name,
            'description' => $hotel->description,
            'slug' => $hotel->slug,
            'stars' => $hotel->stars,
            'featured_image' => $hotel->featured_image,
            'banner' => $hotel->banner,
            'gallery' => $hotel->gallery ?? [],
            'address' => $hotel->address,
            'map_iframe' => $hotel->map_iframe,
            'phone_contact' => $hotel->phone_contact,
            'whatsapp_contact' => $hotel->whatsapp_contact,
            'price_per_night' => $hotel->price_per_night ?? 0,
            'currency' => $hotel->currency ?? 'EGP',
            'formatted_price' => $hotel->formatted_price ?? '0.00 EGP',
            'enabled' => $hotel->enabled,
            'amenities' => $hotel->amenities ? $hotel->amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'icon' => $amenity->icon,
                ];
            }) : [],
            'supplier' => $hotel->supplier ? [
                'id' => $hotel->supplier->id,
                'company_name' => $hotel->supplier->company_name,
                'company_email' => $hotel->supplier->company_email,
                'phone' => $hotel->supplier->phone,
                'website' => $hotel->supplier->website,
                'logo' => $hotel->supplier->logo,
                'banner' => $hotel->supplier->banner,
                'description' => $hotel->supplier->description,
                'commission_rate' => $hotel->supplier->commission_rate ?? 0,
                'is_verified' => $hotel->supplier->is_verified ?? false,
                'is_active' => $hotel->supplier->is_active ?? true,
                'verified_at' => $hotel->supplier->verified_at,
            ] : null,
            'approval_status' => [
                'approved' => $hotel->approved,
                'enabled' => $hotel->enabled,
                'rejection_reason' => $hotel->rejection_reason,
                'status_label' => $this->getStatusLabel($hotel),
                'status_color' => $this->getStatusColor($hotel),
            ],
            'commission' => [
                'rate' => $hotel->supplier->commission_rate ?? 0,
                'rate_formatted' => ($hotel->supplier->commission_rate ?? 0) . '%',
                'amount' => $this->calculateCommissionAmount($hotel),
                'amount_formatted' => $this->formatCommissionAmount($hotel),
            ],
            'recommendation_score' => $this->calculateRecommendationScore($hotel),
            'created_at' => $hotel->created_at,
            'updated_at' => $hotel->updated_at,
        ];
    }

    /**
     * Get status label for supplier hotel
     */
    protected function getStatusLabel($hotel): string
    {
        if (!$hotel->approved) {
            return 'Pending Approval';
        }
        return $hotel->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get status color for supplier hotel
     */
    protected function getStatusColor($hotel): string
    {
        if (!$hotel->approved) {
            return 'warning';
        }
        return $hotel->enabled ? 'success' : 'secondary';
    }

    /**
     * Calculate commission amount for supplier hotel
     */
    protected function calculateCommissionAmount($hotel): float
    {
        $price = $hotel->price_per_night ?? 0;
        $commissionRate = $hotel->supplier->commission_rate ?? 0;
        return ($price * $commissionRate) / 100;
    }

    /**
     * Format commission amount for supplier hotel
     */
    protected function formatCommissionAmount($hotel): string
    {
        $amount = $this->calculateCommissionAmount($hotel);
        $currency = $hotel->currency ?? 'EGP';
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Calculate recommendation score for supplier hotel
     */
    protected function calculateRecommendationScore($hotel): float
    {
        $commissionRate = $hotel->supplier->commission_rate ?? 0;
        $stars = $hotel->stars ?? 0;
        
        // Weighted score: 70% commission rate, 30% stars
        $score = ($commissionRate * 0.7) + ($stars * 0.3);
        
        return round($score, 2);
    }
}

