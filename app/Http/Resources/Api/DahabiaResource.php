<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DahabiaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
            'vessel_length' => $this->vessel_length,
            'capacity' => $this->capacity,
            'price_per_person' => $this->price_per_person,
            'price_per_charter' => $this->price_per_charter,
            'currency' => $this->currency,
            'departure_location' => $this->departure_location,
            'arrival_location' => $this->arrival_location,
            'route_description' => $this->route_description,
            'sailing_schedule' => $this->sailing_schedule,
            'meal_plan' => $this->meal_plan,
            'amenities' => $this->amenities,
            'images' => $this->images,
            'status' => $this->status,
            'crew_count' => $this->crew_count,
            'duration_nights' => $this->duration_nights,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
