<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CombinedTripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if this is a regular trip or supplier trip
        if ($this->resource instanceof \App\Models\Trip) {
            return $this->transformRegularTrip();
        } elseif ($this->resource instanceof \App\Models\SupplierTrip) {
            return $this->transformSupplierTrip();
        }

        return parent::toArray($request);
    }

    /**
     * Transform regular trip data
     */
    protected function transformRegularTrip(): array
    {
        $trip = $this->resource;
        
        return [
            'id' => $trip->id,
            'type' => 'regular',
            'trip_name' => $trip->trip_name,
            'trip_type' => $trip->trip_type,
            'departure_city' => $trip->departureCity ? [
                'id' => $trip->departureCity->id,
                'name' => $trip->departureCity->name,
            ] : null,
            'arrival_city' => $trip->arrivalCity ? [
                'id' => $trip->arrivalCity->id,
                'name' => $trip->arrivalCity->name,
            ] : null,
            'travel_date' => $trip->travel_date,
            'return_date' => $trip->return_date,
            'departure_time' => $trip->departure_time,
            'arrival_time' => $trip->arrival_time,
            'seat_price' => $trip->seat_price,
            'total_seats' => $trip->total_seats,
            'available_seats' => $trip->available_seats,
            'additional_notes' => $trip->additional_notes,
            'amenities' => $trip->amenities ?? [],
            'enabled' => $trip->enabled,
            'supplier' => null, // Regular trips don't have suppliers
            'approval_status' => [
                'approved' => true, // Regular trips are always approved
                'enabled' => $trip->enabled,
                'rejection_reason' => null,
                'status_label' => $trip->enabled ? 'Active' : 'Inactive',
                'status_color' => $trip->enabled ? 'success' : 'secondary',
            ],
            'commission' => [
                'rate' => 0,
                'rate_formatted' => '0%',
                'amount' => 0,
                'amount_formatted' => '0 EGP',
            ],
            'recommendation_score' => 0,
            'created_at' => $trip->created_at,
            'updated_at' => $trip->updated_at,
        ];
    }

    /**
     * Transform supplier trip data
     */
    protected function transformSupplierTrip(): array
    {
        $trip = $this->resource;
        
        return [
            'id' => $trip->id,
            'type' => 'supplier',
            'trip_name' => $trip->trip_name,
            'trip_type' => $trip->trip_type,
            'departure_city' => $trip->departure_city,
            'arrival_city' => $trip->arrival_city,
            'travel_date' => $trip->travel_date,
            'return_date' => $trip->return_date,
            'departure_time' => $trip->departure_time,
            'arrival_time' => $trip->arrival_time,
            'seat_price' => $trip->seat_price,
            'total_seats' => $trip->total_seats,
            'available_seats' => $trip->available_seats,
            'additional_notes' => $trip->additional_notes,
            'amenities' => $trip->amenities ?? [],
            'images' => $trip->images ?? [],
            'featured_image' => $trip->featured_image,
            'enabled' => $trip->enabled,
            'formatted_seat_price' => $trip->formatted_seat_price ?? '0.00 EGP',
            'formatted_total_price' => $trip->formatted_total_price ?? '0.00 EGP',
            'formatted_departure_time' => $trip->formatted_departure_time ?? null,
            'formatted_arrival_time' => $trip->formatted_arrival_time ?? null,
            'trip_type_label' => $trip->trip_type_label ?? null,
            'is_available' => $trip->is_available ?? true,
            'booked_seats' => $trip->booked_seats ?? 0,
            'supplier' => $trip->supplier ? [
                'id' => $trip->supplier->id,
                'company_name' => $trip->supplier->company_name,
                'company_email' => $trip->supplier->company_email,
                'phone' => $trip->supplier->phone,
                'website' => $trip->supplier->website,
                'logo' => $trip->supplier->logo,
                'banner' => $trip->supplier->banner,
                'description' => $trip->supplier->description,
                'commission_rate' => $trip->supplier->commission_rate ?? 0,
                'is_verified' => $trip->supplier->is_verified ?? false,
                'is_active' => $trip->supplier->is_active ?? true,
                'verified_at' => $trip->supplier->verified_at,
            ] : null,
            'approval_status' => [
                'approved' => $trip->approved,
                'enabled' => $trip->enabled,
                'rejection_reason' => $trip->rejection_reason,
                'status_label' => $this->getStatusLabel($trip),
                'status_color' => $this->getStatusColor($trip),
            ],
            'commission' => [
                'rate' => $trip->supplier->commission_rate ?? 0,
                'rate_formatted' => ($trip->supplier->commission_rate ?? 0) . '%',
                'amount' => $this->calculateCommissionAmount($trip),
                'amount_formatted' => $this->formatCommissionAmount($trip),
            ],
            'recommendation_score' => $this->calculateRecommendationScore($trip),
            'created_at' => $trip->created_at,
            'updated_at' => $trip->updated_at,
        ];
    }

    /**
     * Get status label for supplier trip
     */
    protected function getStatusLabel($trip): string
    {
        if (!$trip->approved) {
            return 'Pending Approval';
        }
        return $trip->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get status color for supplier trip
     */
    protected function getStatusColor($trip): string
    {
        if (!$trip->approved) {
            return 'warning';
        }
        return $trip->enabled ? 'success' : 'secondary';
    }

    /**
     * Calculate commission amount for supplier trip
     */
    protected function calculateCommissionAmount($trip): float
    {
        $price = $trip->seat_price ?? 0;
        $commissionRate = $trip->supplier->commission_rate ?? 0;
        return ($price * $commissionRate) / 100;
    }

    /**
     * Format commission amount for supplier trip
     */
    protected function formatCommissionAmount($trip): string
    {
        $amount = $this->calculateCommissionAmount($trip);
        return number_format($amount, 2) . ' EGP';
    }

    /**
     * Calculate recommendation score for supplier trip
     */
    protected function calculateRecommendationScore($trip): float
    {
        $commissionRate = $trip->supplier->commission_rate ?? 0;
        $availableSeats = $trip->available_seats ?? 0;
        $totalSeats = $trip->total_seats ?? 1;
        
        // Weighted score: 70% commission rate, 30% availability
        $availabilityScore = ($availableSeats / $totalSeats) * 100;
        $score = ($commissionRate * 0.7) + ($availabilityScore * 0.3);
        
        return round($score, 2);
    }
}

