<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\JsonResource;
use App\Models\Duration;
use App\Http\Resources\Api\TourResource;

/**
 * @property Duration $resource
 */
class DurationResource extends JsonResource
{
    protected array $relations = [
        'tours' => ['type' => 'collection', 'resourceClass' => '\App\Http\Resources\Api\TourResource'],
        'children' => ['type' => 'collection', 'resourceClass' => '\App\Http\Resources\Api\DurationResource'],
        'seo' => ['type' => 'single', 'resourceClass' => '\App\Http\Resources\Api\SeoResource'],
        'parent' => ['type' => 'single', 'resourceClass' => '\App\Http\Resources\Api\DurationResource'],
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'tours_count' => $this->tours()->where('enabled', true)->count(),
        ];

        // Add tours data if this is a single resource (not in collection)
        // Check if tours are loaded and if this is likely a single resource request
        if ($this->resource->relationLoaded('tours')) {
            $data['tours'] = TourResource::collection($this->tours);
        } else {
            $data['tours'] = [];
        }

        return $data;
    }
} 