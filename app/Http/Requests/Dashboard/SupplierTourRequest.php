<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierTourRequest extends FormRequest
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
            'enabled' => 'Enabled',
            'featured' => 'Featured',
            'slug' => 'Slug',
            'featured_image' => 'Featured Image',
            'gallery' => 'Gallery',
            'days' => 'Tour Days',
            'adult_price' => 'Adult Price',
            'infant_price' => 'Infant Price',
            'child_price' => 'Child Price',
            'pricing_groups' => 'Pricing Groups',
            'categories' => 'Categories',
            'duration_in_days' => 'Duration in days',
            'destinations' => 'Destinations',
            'options' => 'Tour Options',
            'code' => 'Tour Code',
        ];

        // Add pricing group attributes
        for ($i = 0; $i < $this->collect('pricing_groups')->count(); $i++) {
            $attributes['pricing_groups.' . $i . ".from"] = "Group Price From at " . ($i + 1);
            $attributes['pricing_groups.' . $i . ".to"] = "Group Price To at " . ($i + 1);
            $attributes['pricing_groups.' . $i . ".price"] = "Group Adult Price at " . ($i + 1);
            $attributes['pricing_groups.' . $i . ".child_price"] = "Group Child Price at " . ($i + 1);
        }

        // Add translatable field attributes
        foreach (config('translatable.supported_locales') as $localKey => $local) {
            $attributes[$localKey . ".title"] = $local["native"] . " Title";
            $attributes[$localKey . ".overview"] = $local["native"] . " Overview";
            $attributes[$localKey . ".highlights"] = $local["native"] . " Highlights";
            $attributes[$localKey . ".excluded"] = $local["native"] . " Excluded";
            $attributes[$localKey . ".included"] = $local["native"] . " Included";
            $attributes[$localKey . ".duration"] = $local["native"] . " Duration";
            $attributes[$localKey . ".type"] = $local["native"] . " Type";
            $attributes[$localKey . ".run"] = $local["native"] . " Run";
            $attributes[$localKey . ".pickup_time"] = $local["native"] . " PickupTime";
            
            // Add tour days attributes
            for ($i = 0; $i < $this->collect('days')->count(); $i++) {
                $attributes['days.' . $i . '.' . $localKey . ".title"] = $local["native"] . " Day Title at " . ($i + 1);
                $attributes['days.' . $i . '.' . $localKey . ".description"] = $local["native"] . " Day Description at " . ($i + 1);
            }
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
            'code' => ['required', 'string', 'max:255', Rule::unique('supplier_tours')->ignore(request('tour'))],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('supplier_tours')->whereNull('supplier_tours.deleted_at')->ignore(request('tour'))],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'enabled' => ['nullable'],
            'featured' => ['nullable'],
            'featured_image' => ['nullable', 'string'],
            'duration_in_days' => ['nullable', 'integer', 'min:0'],
            'adult_price' => ['required', 'numeric', 'min:0'],
            'infant_price' => ['nullable', 'numeric', 'min:0'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'pricing_groups' => ['nullable', 'array'],
            'pricing_groups.*.from' => ['nullable', 'integer', 'min:1'],
            'pricing_groups.*.to' => ['nullable', 'integer', 'min:1'],
            'pricing_groups.*.price' => ['nullable', 'numeric', 'min:0'],
            'pricing_groups.*.child_price' => ['nullable', 'numeric', 'min:0'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
            'options' => ['nullable', 'array'],
            'options.*' => ['integer', 'exists:tour_options,id'],
            'days' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'destinations' => ['nullable', 'array'],
            'destinations.*' => ['integer', 'exists:destinations,id'],
        ];

        // Add translatable field rules
        foreach (config('translatable.supported_locales') as $locale => $localeName) {
            if ($locale === config('app.locale')) {
                // Main language - required fields
                $rules[$locale . '.title'] = ['required', 'string', 'max:255'];
                $rules[$locale . '.overview'] = ['required', 'string'];
                $rules[$locale . '.duration'] = ['required', 'string'];
                $rules[$locale . '.highlights'] = ['nullable', 'string'];
                $rules[$locale . '.included'] = ['nullable', 'string'];
                $rules[$locale . '.excluded'] = ['nullable', 'string'];
                $rules[$locale . '.type'] = ['nullable', 'string'];
                $rules[$locale . '.run'] = ['nullable', 'string'];
                $rules[$locale . '.pickup_time'] = ['nullable', 'string'];
            } else {
                // Other languages - optional fields
                $rules[$locale . '.title'] = ['nullable', 'string', 'max:255'];
                $rules[$locale . '.overview'] = ['nullable', 'string'];
                $rules[$locale . '.duration'] = ['nullable', 'string'];
                $rules[$locale . '.highlights'] = ['nullable', 'string'];
                $rules[$locale . '.included'] = ['nullable', 'string'];
                $rules[$locale . '.excluded'] = ['nullable', 'string'];
                $rules[$locale . '.type'] = ['nullable', 'string'];
                $rules[$locale . '.run'] = ['nullable', 'string'];
                $rules[$locale . '.pickup_time'] = ['nullable', 'string'];
            }

            // Add tour days rules for each locale
            for ($i = 0; $i < $this->collect('days')->count(); $i++) {
                $rules['days.' . $i . '.' . $locale . '.title'] = ['nullable', 'string', 'max:255'];
                $rules['days.' . $i . '.' . $locale . '.description'] = ['nullable', 'string'];
            }
        }

        return $rules;
    }

    /**
     * Get the validated fields with proper sanitization.
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        
        // Handle checkbox values
        $data['enabled'] = $this->boolean('enabled');
        $data['featured'] = $this->boolean('featured');
        
        // Handle null values properly
        $data['slug'] = $this->handleNullValue($this->input('slug'));
        $data['featured_image'] = $this->handleNullValue($this->input('featured_image'));
        
        // Normalize arrays
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = array_values($data['gallery']);
        }
        if (isset($data['pricing_groups']) && is_array($data['pricing_groups'])) {
            $data['pricing_groups'] = array_values($data['pricing_groups']);
        }
        
        // Remove translatable fields from main data (they'll be handled separately)
        foreach (config('translatable.supported_locales') as $locale => $localeName) {
            unset($data[$locale]);
        }
        
        // Remove relationship fields (they'll be handled separately)
        unset($data['categories'], $data['destinations'], $data['options'], $data['days']);
        
        return $data;
    }

    /**
     * Handle null values properly.
     *
     * @param mixed $value
     * @return mixed
     */
    private function handleNullValue($value)
    {
        return ($value === 'null' || $value === null || $value === '') ? null : $value;
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

    /**
     * Get relationship data.
     *
     * @return array
     */
    public function getRelationshipData(): array
    {
        return [
            'categories' => $this->input('categories', []),
            'destinations' => $this->input('destinations', []),
            'options' => $this->input('options', []),
        ];
    }
}



