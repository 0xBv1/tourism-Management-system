<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CombinedTourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if this is a regular tour or supplier tour
        if ($this->resource instanceof \App\Models\Tour) {
            return $this->transformRegularTour();
        } elseif ($this->resource instanceof \App\Models\SupplierTour) {
            return $this->transformSupplierTour();
        }

        return parent::toArray($request);
    }

    /**
     * Transform regular tour data
     */
    protected function transformRegularTour(): array
    {
        $tour = $this->resource;
        
        return [
            'id' => $tour->id,
            'type' => 'regular',
            'title' => $tour->title,
            'slug' => $tour->slug,
            'overview' => $tour->overview,
            'highlights' => $tour->highlights,
            'included' => $tour->included,
            'excluded' => $tour->excluded,
            'duration' => $tour->duration,
            'type' => $tour->type,
            'run' => $tour->run,
            'pickup_time' => $tour->pickup_time,
            'featured_image' => $tour->featured_image,
            'gallery' => $tour->gallery ?? [],
            'adult_price' => $tour->adult_price,
            'child_price' => $tour->child_price,
            'infant_price' => $tour->infant_price,
            'currency' => 'EGP', // Default currency for regular tours
            'featured' => $tour->featured,
            'enabled' => $tour->enabled,
            'code' => $tour->code,
            'duration_in_days' => $tour->duration_in_days,
            'pricing_groups' => $tour->pricing_groups,
            'destinations' => $tour->destinations ? $tour->destinations->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                ];
            }) : [],
            'categories' => $tour->categories ? $tour->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }) : [],
            'options' => $tour->options ? $tour->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => $option->price,
                ];
            }) : [],
            'supplier' => null, // Regular tours don't have suppliers
            'approval_status' => [
                'approved' => true, // Regular tours are always approved
                'enabled' => $tour->enabled,
                'rejection_reason' => null,
                'status_label' => $tour->enabled ? 'Active' : 'Inactive',
                'status_color' => $tour->enabled ? 'success' : 'secondary',
            ],
            'commission' => [
                'rate' => 0,
                'rate_formatted' => '0%',
                'amount' => 0,
                'amount_formatted' => '0 EGP',
            ],
            'recommendation_score' => 0,
            'created_at' => $tour->created_at,
            'updated_at' => $tour->updated_at,
        ];
    }

    /**
     * Transform supplier tour data
     */
    protected function transformSupplierTour(): array
    {
        $tour = $this->resource;
        
        return [
            'id' => $tour->id,
            'type' => 'supplier',
            'title' => $tour->title,
            'slug' => $tour->slug,
            'overview' => $tour->overview,
            'highlights' => $tour->highlights,
            'included' => $tour->included,
            'excluded' => $tour->excluded,
            'duration' => $tour->duration,
            'type' => $tour->type,
            'run' => $tour->run,
            'pickup_time' => $tour->pickup_time,
            'featured_image' => $tour->featured_image,
            'gallery' => $tour->gallery ?? [],
            'images' => $tour->images ?? [],
            'adult_price' => $tour->adult_price,
            'child_price' => $tour->child_price,
            'infant_price' => $tour->infant_price,
            'currency' => $tour->currency ?? 'EGP',
            'featured' => $tour->featured,
            'enabled' => $tour->enabled,
            'code' => $tour->code,
            'duration_in_days' => $tour->duration_in_days,
            'pricing_groups' => $tour->pricing_groups,
            'max_group_size' => $tour->max_group_size,
            'pickup_location' => $tour->pickup_location,
            'dropoff_location' => $tour->dropoff_location,
            'itinerary' => $tour->itinerary,
            'supplier' => $tour->supplier ? [
                'id' => $tour->supplier->id,
                'company_name' => $tour->supplier->company_name,
                'company_email' => $tour->supplier->company_email,
                'phone' => $tour->supplier->phone,
                'website' => $tour->supplier->website,
                'logo' => $tour->supplier->logo,
                'banner' => $tour->supplier->banner,
                'description' => $tour->supplier->description,
                'commission_rate' => $tour->supplier->commission_rate ?? 0,
                'is_verified' => $tour->supplier->is_verified ?? false,
                'is_active' => $tour->supplier->is_active ?? true,
                'verified_at' => $tour->supplier->verified_at,
            ] : null,
            'approval_status' => [
                'approved' => $tour->approved,
                'enabled' => $tour->enabled,
                'rejection_reason' => $tour->rejection_reason,
                'status_label' => $this->getStatusLabel($tour),
                'status_color' => $this->getStatusColor($tour),
            ],
            'commission' => [
                'rate' => $tour->supplier->commission_rate ?? 0,
                'rate_formatted' => ($tour->supplier->commission_rate ?? 0) . '%',
                'amount' => $this->calculateCommissionAmount($tour),
                'amount_formatted' => $this->formatCommissionAmount($tour),
            ],
            'recommendation_score' => $this->calculateRecommendationScore($tour),
            'created_at' => $tour->created_at,
            'updated_at' => $tour->updated_at,
        ];
    }

    /**
     * Get status label for supplier tour
     */
    protected function getStatusLabel($tour): string
    {
        if (!$tour->approved) {
            return 'Pending Approval';
        }
        return $tour->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get status color for supplier tour
     */
    protected function getStatusColor($tour): string
    {
        if (!$tour->approved) {
            return 'warning';
        }
        return $tour->enabled ? 'success' : 'secondary';
    }

    /**
     * Calculate commission amount for supplier tour
     */
    protected function calculateCommissionAmount($tour): float
    {
        $price = $tour->adult_price ?? 0;
        $commissionRate = $tour->supplier->commission_rate ?? 0;
        return ($price * $commissionRate) / 100;
    }

    /**
     * Format commission amount for supplier tour
     */
    protected function formatCommissionAmount($tour): string
    {
        $amount = $this->calculateCommissionAmount($tour);
        $currency = $tour->currency ?? 'EGP';
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Calculate recommendation score for supplier tour
     */
    protected function calculateRecommendationScore($tour): float
    {
        $commissionRate = $tour->supplier->commission_rate ?? 0;
        $rating = $tour->rating ?? 0;
        $stars = $tour->stars ?? 0;
        
        // Weighted score: 70% commission rate, 20% rating, 10% stars
        $score = ($commissionRate * 0.7) + ($rating * 0.2) + ($stars * 0.1);
        
        return round($score, 2);
    }
}

