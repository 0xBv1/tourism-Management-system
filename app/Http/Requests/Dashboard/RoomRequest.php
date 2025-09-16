<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
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
            "amenities" => "Amenities",
            "slug" => "Slug",
            "featured_image" => "Featured Image",
            "banner" => "Banner",
            "gallery" => "Gallery",
            "enabled" => "Enabled",
            "bed_count" => "Bed Count",
            "room_type" => "Room Type",
            "max_capacity" => "Max Capacity",
            "bed_types" => "Bed Types",
            "night_price" => "Night Price",
            "extra_bed_available" => "Extra Bed Available",
            "extra_bed_price" => "Extra Bed Price",
            "max_extra_beds" => "Maximum Extra Beds",
            "extra_bed_description" => "Extra Bed Description",
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
            'slug' => ['nullable', Rule::unique('rooms')->ignore(request('room'))],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'banner' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['nullable', 'integer', 'exists:amenities,id'],
            'enabled' => ['nullable'],
            'bed_count' => ['integer', 'min:1'],
            'room_type' => ['nullable', 'string', 'max:255'],
            'max_capacity' => ['nullable', 'integer', 'min:0'],
            'bed_types' => ['nullable', 'string', 'max:255'],
            'night_price' => ['required', 'numeric', 'min:0'],
            'extra_bed_available' => ['nullable'],
            'extra_bed_price' => ['nullable', 'numeric', 'min:0'],
            'max_extra_beds' => ['nullable', 'integer', 'min:0', 'max:5'],
            'extra_bed_description' => ['nullable', 'string'],
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
        $data['extra_bed_available'] = $this->boolean('extra_bed_available');
        
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
