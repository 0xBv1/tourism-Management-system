<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            "transport_type" => "Transport Type",
            "vehicle_type" => "Vehicle Type",
            "seating_capacity" => "Seating Capacity",
            "origin_location" => "Origin Location",
            "destination_location" => "Destination Location",
            "intermediate_stops" => "Intermediate Stops",
            "estimated_travel_time" => "Estimated Travel Time",
            "distance" => "Distance",
            "route_type" => "Route Type",
            "price" => "Price",
            "currency" => "Currency",
            "vehicle_registration" => "Vehicle Registration",
            "amenities" => "Amenities",
            "images" => "Images",
            "featured_image" => "Featured Image",
            "enabled" => "Enabled",
            "slug" => "Slug",
            "phone_contact" => "Phone Contact",
            "whatsapp_contact" => "WhatsApp Contact",
            "email_contact" => "Email Contact",
            "contact_notes" => "Contact Notes",
            "departure_time" => "Departure Time",
            "arrival_time" => "Arrival Time",
            "departure_location" => "Departure Location",
            "arrival_location" => "Arrival Location",
            "schedule_notes" => "Schedule Notes",
            "price_per_hour" => "Price Per Hour",
            "price_per_day" => "Price Per Day",
            "price_per_km" => "Price Per KM",
            "discount_percentage" => "Discount Percentage",
            "discount_conditions" => "Discount Conditions",
            "pricing_notes" => "Pricing Notes",
            "vehicle_images" => "Vehicle Images",
            "route_map" => "Route Map",
        ];
        
        foreach (config('translatable.supported_locales') as $localKey => $local) {
            $attributes[$localKey . ".name"] = $local["native"] . " Name";
            $attributes[$localKey . ".description"] = $local["native"] . " Description";
            $attributes['seo.' . $localKey . ".meta_title"] = $local["native"] . " Meta Title";
            $attributes['seo.' . $localKey . ".meta_description"] = $local["native"] . " Meta Description";
            $attributes['seo.' . $localKey . ".meta_keywords"] = $local["native"] . " Meta Keywords";
            $attributes['seo.' . $localKey . ".og_title"] = $local["native"] . " Open Graph Title";
            $attributes['seo.' . $localKey . ".og_description"] = $local["native"] . " Open Graph Description";
            $attributes['seo.' . $localKey . ".canonical"] = $local["native"] . " Canonical";
        }

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'slug' => ['nullable', 'string', Rule::unique('transports')->ignore(request('transport'))],
            'transport_type' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
            'seating_capacity' => ['nullable', 'integer', 'min:1'],
            'origin_location' => ['required', 'string', 'max:255'],
            'destination_location' => ['required', 'string', 'max:255'],
            'intermediate_stops' => ['nullable', 'string'],
            'estimated_travel_time' => ['nullable', 'integer', 'min:1'],
            'distance' => ['nullable', 'numeric', 'min:0'],
            'route_type' => ['required', 'string', 'in:direct,with_stops,circular'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'vehicle_registration' => ['nullable', 'string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['nullable', 'integer', 'exists:amenities,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'enabled' => ['nullable'],
            'phone_contact' => ['nullable', 'string', 'max:255'],
            'whatsapp_contact' => ['nullable', 'string', 'max:255'],
            'email_contact' => ['nullable', 'email', 'max:255'],
            'contact_notes' => ['nullable', 'string'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'arrival_time' => ['nullable', 'date_format:H:i'],
            'departure_location' => ['nullable', 'string', 'max:255'],
            'arrival_location' => ['nullable', 'string', 'max:255'],
            'schedule_notes' => ['nullable', 'string'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'price_per_km' => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_conditions' => ['nullable', 'string'],
            'pricing_notes' => ['nullable', 'string'],
            'vehicle_images' => ['nullable', 'array'],
            'vehicle_images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'route_map' => ['nullable', 'string', 'max:255'],
            'seo' => ['nullable', 'array'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
            'seo.og_type' => ['nullable', 'string', 'max:255'],
            'seo.viewport' => ['nullable', 'string', 'max:255'],
            'seo.robots' => ['nullable', 'string', 'max:255'],
        ];
        
        foreach (config('translatable.locales') as $local) {
            $rules["$local.name"] = [($local == config("app.locale") ? "required" : "nullable"), 'string', 'max:255'];
            $rules["$local.description"] = ["nullable"];
            $rules["seo.$local.meta_title"] = ["nullable", 'string', 'max:255'];
            $rules["seo.$local.meta_description"] = ["nullable", 'string', 'max:255'];
            $rules["seo.$local.meta_keywords"] = ["nullable", 'string', 'max:255'];
            $rules["seo.$local.og_title"] = ["nullable", 'string', 'max:255'];
            $rules["seo.$local.og_description"] = ["nullable", 'string', 'max:255'];
            $rules["seo.$local.canonical"] = ["nullable", 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['enabled'] = $this->boolean('enabled');
        
        // Remove translatable fields from main data (they'll be handled separately)
        foreach (config('translatable.supported_locales') as $locale => $localeName) {
            unset($data[$locale]);
        }
        
        unset($data['seo']);
        unset($data['amenities']);
        return $data;
    }

    /**
     * Get translatable data for each locale.
     *
     * @return array
     */
    public function getTranslatableData(): array
    {
        $translatableData = [];
        
        foreach (config('translatable.supported_locales') as $locale => $localeName) {
            if ($this->has($locale)) {
                $translatableData[$locale] = $this->input($locale);
            }
        }
        
        return $translatableData;
    }
}
