<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierTransportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['Supplier', 'Supplier Admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Basic Information
            'origin_location' => 'required|string|max:255',
            'destination_location' => 'required|string|max:255',
            'intermediate_stops' => 'nullable|string|max:1000',
            'estimated_travel_time' => 'required|integer|min:1|max:1440', // Max 24 hours in minutes
            'distance' => 'nullable|numeric|min:0|max:10000', // Max 10,000 km
            'route_type' => [
                'required',
                'string',
                'max:100',
                Rule::in(['direct', 'with_stops', 'circular'])
            ],
            'price' => 'required|numeric|min:0|max:999999.99',
            'currency' => [
                'nullable',
                'string',
                'max:3',
                Rule::in(['USD', 'EUR', 'GBP', 'EGP', 'AED', 'SAR', 'QAR', 'KWD'])
            ],
            'vehicle_type' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(['sedan', 'suv', 'van', 'bus', 'train', 'boat', 'plane', 'helicopter', 'limousine', 'motorcycle'])
            ],
            'seating_capacity' => 'nullable|integer|min:1|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => 'nullable|integer|exists:amenities,id',
            'enabled' => 'nullable',
            'transport_type' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(['bus', 'train', 'ferry', 'plane', 'car', 'van', 'boat', 'helicopter'])
            ],
            'vehicle_registration' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',

            // Schedule fields
            'departure_time' => 'nullable|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'departure_location' => 'nullable|string|max:255',
            'arrival_location' => 'nullable|string|max:255',
            'schedule_notes' => 'nullable|string|max:1000',

            // Pricing fields
            'price_per_hour' => 'nullable|numeric|min:0|max:999999.99',
            'price_per_day' => 'nullable|numeric|min:0|max:999999.99',
            'price_per_km' => 'nullable|numeric|min:0|max:999999.99',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_conditions' => 'nullable|string|max:255',
            'pricing_notes' => 'nullable|string|max:1000',

            // Contact fields
            'contact_person' => 'nullable|string|max:255',
            'phone_contact' => 'nullable|string|max:255',
            'whatsapp_contact' => 'nullable|string|max:255',
            'email_contact' => 'nullable|email|max:255',
            'contact_notes' => 'nullable|string|max:1000',

            // Media fields
            'featured_image' => 'nullable|string|max:255',
            'vehicle_images' => 'nullable|array',
            'vehicle_images.*' => 'nullable|string|max:255',
            'route_map' => 'nullable|string|max:255',

            // SEO fields
            'seo' => 'nullable|array',
            'seo.title' => 'nullable|string|max:255',
            'seo.description' => 'nullable|string|max:500',
            'seo.keywords' => 'nullable|string|max:500',

            // Translatable fields
            'en' => 'nullable|array',
            'en.name' => 'nullable|string|max:255',
            'en.description' => 'nullable|string|max:1000',
            'fr' => 'nullable|array',
            'fr.name' => 'nullable|string|max:255',
            'fr.description' => 'nullable|string|max:1000',
            'de' => 'nullable|array',
            'de.name' => 'nullable|string|max:255',
            'de.description' => 'nullable|string|max:1000',
            'it' => 'nullable|array',
            'it.name' => 'nullable|string|max:255',
            'it.description' => 'nullable|string|max:1000',
            'pt' => 'nullable|array',
            'pt.name' => 'nullable|string|max:255',
            'pt.description' => 'nullable|string|max:1000',
            'es' => 'nullable|array',
            'es.name' => 'nullable|string|max:255',
            'es.description' => 'nullable|string|max:1000',
            'zh' => 'nullable|array',
            'zh.name' => 'nullable|string|max:255',
            'zh.description' => 'nullable|string|max:1000',

        ];



        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            // Basic Information
            'origin_location.required' => 'Origin location is required.',
            'origin_location.max' => 'Origin location cannot exceed 255 characters.',
            
            'destination_location.required' => 'Destination location is required.',
            'destination_location.max' => 'Destination location cannot exceed 255 characters.',
            
            'intermediate_stops.max' => 'Intermediate stops cannot exceed 1000 characters.',
            
            'estimated_travel_time.required' => 'Travel time is required.',
            'estimated_travel_time.integer' => 'Travel time must be a whole number.',
            'estimated_travel_time.min' => 'Travel time must be at least 1 minute.',
            'estimated_travel_time.max' => 'Travel time cannot exceed 24 hours.',
            
            'distance.numeric' => 'Distance must be a number.',
            'distance.min' => 'Distance cannot be negative.',
            'distance.max' => 'Distance cannot exceed 10,000 km.',
            
            'route_type.required' => 'Route type is required.',
            'route_type.in' => 'Please select a valid route type.',
            
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be greater than 0.',
            'price.max' => 'Price cannot exceed 999,999.99.',
            
            'currency.in' => 'Please select a valid currency.',
            
            'vehicle_type.in' => 'Please select a valid vehicle type.',
            
            'seating_capacity.integer' => 'Seating capacity must be a whole number.',
            'seating_capacity.min' => 'Seating capacity must be at least 1.',
            'seating_capacity.max' => 'Seating capacity cannot exceed 100.',
            
            'amenities.array' => 'Amenities must be an array.',
            'amenities.*.integer' => 'Amenity ID must be a number.',
            'amenities.*.exists' => 'Selected amenity does not exist.',

            // Schedule fields
            'departure_time.date_format' => 'Departure time must be in HH:MM format.',
            'arrival_time.date_format' => 'Arrival time must be in HH:MM format.',
            'departure_location.max' => 'Departure location cannot exceed 255 characters.',
            'arrival_location.max' => 'Arrival location cannot exceed 255 characters.',
            'schedule_notes.max' => 'Schedule notes cannot exceed 1000 characters.',

            // Pricing fields
            'price_per_hour.numeric' => 'Price per hour must be a number.',
            'price_per_hour.min' => 'Price per hour cannot be negative.',
            'price_per_hour.max' => 'Price per hour cannot exceed 999,999.99.',
            
            'price_per_day.numeric' => 'Price per day must be a number.',
            'price_per_day.min' => 'Price per day cannot be negative.',
            'price_per_day.max' => 'Price per day cannot exceed 999,999.99.',
            
            'price_per_km.numeric' => 'Price per km must be a number.',
            'price_per_km.min' => 'Price per km cannot be negative.',
            'price_per_km.max' => 'Price per km cannot exceed 999,999.99.',
            
            'discount_percentage.numeric' => 'Discount percentage must be a number.',
            'discount_percentage.min' => 'Discount percentage cannot be negative.',
            'discount_percentage.max' => 'Discount percentage cannot exceed 100%.',
            
            'discount_conditions.max' => 'Discount conditions cannot exceed 255 characters.',
            'pricing_notes.max' => 'Pricing notes cannot exceed 1000 characters.',

            // Contact fields
            'contact_person.max' => 'Contact person name cannot exceed 255 characters.',
            'phone_contact.max' => 'Phone contact cannot exceed 255 characters.',
            'whatsapp_contact.max' => 'WhatsApp contact cannot exceed 255 characters.',
            'email_contact.email' => 'Please enter a valid email address.',
            'email_contact.max' => 'Email contact cannot exceed 255 characters.',
            'contact_notes.max' => 'Contact notes cannot exceed 1000 characters.',

            // Media fields
            'featured_image.max' => 'Featured image path cannot exceed 255 characters.',
            'vehicle_images.array' => 'Vehicle images must be an array.',
            'vehicle_images.*.max' => 'Vehicle image path cannot exceed 255 characters.',
            'route_map.max' => 'Route map path cannot exceed 255 characters.',

            // New fields
            'transport_type.in' => 'Please select a valid transport type.',
            'vehicle_registration.max' => 'Vehicle registration cannot exceed 255 characters.',
            'slug.max' => 'Slug cannot exceed 255 characters.',

            // SEO fields
            'seo.array' => 'SEO data must be an array.',
            'seo.title.max' => 'SEO title cannot exceed 255 characters.',
            'seo.description.max' => 'SEO description cannot exceed 500 characters.',
            'seo.keywords.max' => 'SEO keywords cannot exceed 500 characters.',

            // Translatable fields
            'en.array' => 'English data must be an array.',
            'en.name.max' => 'English name cannot exceed 255 characters.',
            'en.description.max' => 'English description cannot exceed 1000 characters.',
            'fr.array' => 'French data must be an array.',
            'fr.name.max' => 'French name cannot exceed 255 characters.',
            'fr.description.max' => 'French description cannot exceed 1000 characters.',
            'de.array' => 'German data must be an array.',
            'de.name.max' => 'German name cannot exceed 255 characters.',
            'de.description.max' => 'German description cannot exceed 1000 characters.',
            'it.array' => 'Italian data must be an array.',
            'it.name.max' => 'Italian name cannot exceed 255 characters.',
            'it.description.max' => 'Italian description cannot exceed 1000 characters.',
            'pt.array' => 'Portuguese data must be an array.',
            'pt.name.max' => 'Portuguese name cannot exceed 255 characters.',
            'pt.description.max' => 'Portuguese description cannot exceed 1000 characters.',
            'es.array' => 'Spanish data must be an array.',
            'es.name.max' => 'Spanish name cannot exceed 255 characters.',
            'es.description.max' => 'Spanish description cannot exceed 1000 characters.',
            'zh.array' => 'Chinese data must be an array.',
            'zh.name.max' => 'Chinese name cannot exceed 255 characters.',
            'zh.description.max' => 'Chinese description cannot exceed 1000 characters.',

        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'origin_location' => 'origin location',
            'destination_location' => 'destination location',
            'intermediate_stops' => 'intermediate stops',
            'estimated_travel_time' => 'travel time',
            'route_type' => 'route type',
            'vehicle_type' => 'vehicle type',
            'seating_capacity' => 'seating capacity',
            'departure_time' => 'departure time',
            'arrival_time' => 'arrival time',
            'departure_location' => 'departure location',
            'arrival_location' => 'arrival location',
            'schedule_notes' => 'schedule notes',
            'price_per_hour' => 'price per hour',
            'price_per_day' => 'price per day',
            'price_per_km' => 'price per km',
            'discount_percentage' => 'discount percentage',
            'discount_conditions' => 'discount conditions',
            'pricing_notes' => 'pricing notes',
            'contact_person' => 'contact person',
            'phone_contact' => 'phone contact',
            'whatsapp_contact' => 'whatsapp contact',
            'email_contact' => 'email contact',
            'contact_notes' => 'contact notes',
            'featured_image' => 'featured image',
            'vehicle_images' => 'vehicle images',
            'route_map' => 'route map',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up intermediate stops and amenities
        if ($this->has('intermediate_stops') && !empty($this->intermediate_stops)) {
            $this->merge([
                'intermediate_stops' => trim($this->intermediate_stops)
            ]);
        }

        if ($this->has('amenities') && !empty($this->amenities)) {
            $amenities = $this->amenities;
            
            // Handle different formats of amenities
            if (is_string($amenities)) {
                // If it's a comma-separated string, convert to array
                $amenities = array_filter(array_map('trim', explode(',', $amenities)));
            } elseif (is_array($amenities)) {
                // If it's already an array, just ensure it's clean
                $amenities = array_filter(array_map('trim', $amenities));
            } else {
                $amenities = [];
            }
            
            $this->merge([
                'amenities' => $amenities
            ]);
        }

        // Set default values
        if (!$this->has('currency') || empty($this->currency)) {
            $this->merge(['currency' => 'EGP']);
        }

        if (!$this->has('enabled')) {
            $this->merge(['enabled' => true]);
        }




    }

    // Removed custom failedValidation to use Laravel's default validation behavior
    // This will redirect back to the form with proper error messages instead of JSON response

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        $validated['enabled'] = $this->boolean('enabled');

        // Add supplier_id for new transports
        if ($this->isMethod('POST')) {
            $validated['supplier_id'] = auth()->user()->supplier->id;
            $validated['approved'] = false; // New transports need admin approval
        }

        return $validated;
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
