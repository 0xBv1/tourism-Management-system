<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NileCruiseResource extends JsonResource
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
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                ];
            }),
            'vessel_type' => $this->vessel_type,
            'capacity' => $this->capacity,
            'price_per_person' => $this->price_per_person,
            'price_per_cabin' => $this->price_per_cabin,
            'currency' => $this->currency,
            'departure_location' => $this->departure_location,
            'arrival_location' => $this->arrival_location,
            'itinerary' => $this->itinerary,
            'meal_plan' => $this->meal_plan,
            'amenities' => $this->amenities,
            'images' => $this->images,
            'status' => $this->status,
            'check_in_time' => $this->check_in_time ? $this->check_in_time->format('H:i') : null,
            'check_out_time' => $this->check_out_time ? $this->check_out_time->format('H:i') : null,
            'duration_nights' => $this->duration_nights,
            'notes' => $this->notes,
            'active' => $this->active,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
