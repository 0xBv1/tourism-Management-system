<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierHotelRequest extends FormRequest
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
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [
            'stars' => 'Stars',
            'enabled' => 'Enabled',
            'featured_image' => 'Featured Image',
            'banner' => 'Banner',
            'gallery' => 'Gallery',
            'address' => 'Address',
            'map_iframe' => 'Map Iframe',
            'slug' => 'Slug',
            'phone_contact' => 'Phone Contact',
            'whatsapp_contact' => 'Whatsapp Contact',
            'amenities' => 'Amenities',
            'approved' => 'Approved',
            'rejection_reason' => 'Rejection Reason',
        ];
        
        foreach (config('translatable.supported_locales') as $localKey => $local) {
            $attributes[$localKey . ".name"] = $local["native"] . " Name";
            $attributes[$localKey . ".description"] = $local["native"] . " Description";
            $attributes[$localKey . ".city"] = $local["native"] . " City";
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
            'slug' => ['nullable', 'string', Rule::unique('supplier_hotels')->ignore(request('supplier_hotel'))],
            'stars' => ['nullable', 'integer', 'min:1', 'max:5'],
            'enabled' => ['nullable'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'banner' => ['nullable', 'string', 'max:255'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['nullable', 'integer', 'exists:amenities,id'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
            'address' => ['nullable', 'string', 'max:255'],
            'map_iframe' => ['nullable', 'string'],
            'phone_contact' => ['nullable', 'string', 'max:255'],
            'whatsapp_contact' => ['nullable', 'string', 'max:255'],
            'approved' => ['nullable'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
            'seo' => ['nullable', 'array'],
            'seo.og_image' => ['nullable', 'string', 'max:255'],
            'seo.og_type' => ['nullable', 'string', 'max:255'],
            'seo.viewport' => ['nullable', 'string', 'max:255'],
            'seo.robots' => ['nullable', 'string', 'max:255'],
        ];
        
        foreach (config('translatable.locales') as $local) {
            $rules["$local.name"] = [($local == config("app.locale") ? "required" : "nullable"), 'string', 'max:255'];
            $rules["$local.description"] = ["nullable"];
            $rules["$local.city"] = ["nullable", 'string', 'max:255'];
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
        $data['approved'] = $this->boolean('approved');
        unset($data['seo']);
        unset($data['amenities']);
        return $data;
    }
}



