<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                ];
            }),
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'cuisine_type' => $this->cuisine_type,
            'price_range' => $this->price_range,
            'price_per_meal' => $this->price_per_meal,
            'currency' => $this->currency,
            'cuisines' => $this->cuisines,
            'features' => $this->features,
            'amenities' => $this->amenities,
            'images' => $this->images,
            'status' => $this->status,
            'opening_hours' => $this->opening_hours,
            'capacity' => $this->capacity,
            'reservation_required' => $this->reservation_required,
            'dress_code' => $this->dress_code,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
