<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        
        // Get site name from settings
        $siteName = site_name();
        
        // Add supplier information
        $data['supplier'] = [
            'id' => $this->supplier->id ?? null,
            'company_name' => $this->supplier->company_name ?? $siteName,
            'company_email' => $this->supplier->company_email ?? null,
            'phone' => $this->supplier->phone ?? null,
            'website' => $this->supplier->website ?? null,
            'logo' => $this->supplier->logo ?? null,
            'banner' => $this->supplier->banner ?? null,
            'description' => $this->supplier->description ?? null,
            'commission_rate' => $this->supplier->commission_rate ?? 0,
            'is_verified' => $this->supplier->is_verified ?? false,
            'is_active' => $this->supplier->is_active ?? true,
            'verified_at' => $this->supplier->verified_at ?? null,
        ];

        // Add service type information
        $data['service_type'] = $this->service_type ?? $this->getServiceType();
        $data['service_name'] = $this->service_name ?? $this->getName();
        $data['service_price'] = $this->service_price ?? $this->getPrice();
        $data['service_currency'] = $this->service_currency ?? $this->getCurrency();

        // Add approval and status information
        $data['approval_status'] = [
            'approved' => $this->approved ?? false,
            'enabled' => $this->enabled ?? false,
            'rejection_reason' => $this->rejection_reason ?? null,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
        ];

        // Add commission information
        $data['commission'] = [
            'rate' => $this->supplier->commission_rate ?? 0,
            'rate_formatted' => ($this->supplier->commission_rate ?? 0) . '%',
            'amount' => $this->calculateCommissionAmount(),
            'amount_formatted' => $this->formatCommissionAmount(),
        ];

        // Add recommendation score
        $data['recommendation_score'] = $this->calculateRecommendationScore();

        // Add location information
        $data['location'] = [
            'city' => $this->city ?? $this->getCity(),
            'address' => $this->address ?? null,
            'map_iframe' => $this->map_iframe ?? null,
        ];

        // Add contact information
        $data['contact'] = [
            'phone' => $this->phone_contact ?? $this->supplier->phone ?? null,
            'whatsapp' => $this->whatsapp_contact ?? null,
            'email' => $this->supplier->company_email ?? null,
        ];

        // Add media information
        $data['media'] = [
            'featured_image' => $this->featured_image ?? null,
            'banner' => $this->banner ?? null,
            'gallery' => $this->gallery ?? [],
            'images' => $this->images ?? [],
        ];

        // Add SEO information
        $data['seo'] = [
            'slug' => $this->slug ?? null,
            'meta_title' => $this->meta_title ?? null,
            'meta_description' => $this->meta_description ?? null,
            'meta_keywords' => $this->meta_keywords ?? null,
        ];

        // Add additional service-specific information
        $data['details'] = $this->getServiceDetails();

        return $data;
    }

    /**
     * Get service type.
     */
    protected function getServiceType(): string
    {
        if (isset($this->service_type)) {
            return $this->service_type;
        }

        if ($this->resource instanceof \App\Models\SupplierHotel) {
            return 'Hotel';
        } elseif ($this->resource instanceof \App\Models\SupplierTour) {
            return 'Tour';
        } elseif ($this->resource instanceof \App\Models\SupplierTrip) {
            return 'Trip';
        } elseif ($this->resource instanceof \App\Models\SupplierTransport) {
            return 'Transport';
        }

        return 'Service';
    }

    /**
     * Get service name.
     */
    protected function getName(): string
    {
        if (isset($this->service_name)) {
            return $this->service_name;
        }

        return $this->name ?? $this->title ?? 'Unnamed Service';
    }

    /**
     * Get service price.
     */
    protected function getPrice(): float
    {
        if (isset($this->service_price)) {
            return (float) $this->service_price;
        }

        return (float) ($this->price ?? $this->price_per_night ?? $this->seat_price ?? 0);
    }

    /**
     * Get service currency.
     */
    protected function getCurrency(): string
    {
        if (isset($this->service_currency)) {
            return $this->service_currency;
        }

        return $this->currency ?? 'EGP';
    }

    /**
     * Get service city.
     */
    protected function getCity(): string
    {
        if (isset($this->city)) {
            return $this->city;
        }

        return $this->departure_city ?? $this->origin_location ?? '';
    }

    /**
     * Get status label.
     */
    protected function getStatusLabel(): string
    {
        if (!$this->approved) {
            return 'Pending Approval';
        }
        return $this->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get status color.
     */
    protected function getStatusColor(): string
    {
        if (!$this->approved) {
            return 'warning';
        }
        return $this->enabled ? 'success' : 'secondary';
    }

    /**
     * Calculate commission amount.
     */
    protected function calculateCommissionAmount(): float
    {
        $price = $this->getPrice();
        $commissionRate = $this->supplier->commission_rate ?? 0;
        return ($price * $commissionRate) / 100;
    }

    /**
     * Format commission amount.
     */
    protected function formatCommissionAmount(): string
    {
        $amount = $this->calculateCommissionAmount();
        $currency = $this->getCurrency();
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Calculate recommendation score based on commission rate and quality.
     */
    protected function calculateRecommendationScore(): float
    {
        $commissionRate = $this->supplier->commission_rate ?? 0;
        $rating = $this->rating ?? 0;
        $stars = $this->stars ?? 0;
        
        // Weighted score: 70% commission rate, 20% rating, 10% stars
        $score = ($commissionRate * 0.7) + ($rating * 0.2) + ($stars * 0.1);
        
        return round($score, 2);
    }

    /**
     * Get service-specific details.
     */
    protected function getServiceDetails(): array
    {
        $details = [];

        switch ($this->getServiceType()) {
            case 'Hotel':
                $details = [
                    'stars' => $this->stars ?? null,
                    'amenities' => $this->amenities ?? [],
                    'check_in_time' => $this->check_in_time ?? null,
                    'check_out_time' => $this->check_out_time ?? null,
                ];
                break;

            case 'Tour':
                $details = [
                    'duration' => $this->duration ?? null,
                    'group_size' => $this->group_size ?? null,
                    'included_services' => $this->included_services ?? [],
                    'excluded_services' => $this->excluded_services ?? [],
                    'itinerary' => $this->itinerary ?? [],
                ];
                break;

            case 'Trip':
                $details = [
                    'trip_type' => $this->trip_type ?? null,
                    'departure_city' => $this->departure_city ?? null,
                    'arrival_city' => $this->arrival_city ?? null,
                    'travel_date' => $this->travel_date ?? null,
                    'return_date' => $this->return_date ?? null,
                    'departure_time' => $this->departure_time ?? null,
                    'arrival_time' => $this->arrival_time ?? null,
                    'total_seats' => $this->total_seats ?? null,
                    'available_seats' => $this->available_seats ?? null,
                ];
                break;

            case 'Transport':
                $details = [
                    'origin_location' => $this->origin_location ?? null,
                    'destination_location' => $this->destination_location ?? null,
                    'intermediate_stops' => $this->intermediate_stops ?? [],
                    'estimated_travel_time' => $this->estimated_travel_time ?? null,
                    'distance' => $this->distance ?? null,
                    'route_type' => $this->route_type ?? null,
                    'vehicle_type' => $this->vehicle_type ?? null,
                    'seating_capacity' => $this->seating_capacity ?? null,
                    'amenities' => $this->amenities ?? [],
                ];
                break;
        }

        return $details;
    }
}
