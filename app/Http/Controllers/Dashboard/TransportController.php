<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\TransportDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\TransportRequest;
use App\Models\Transport;
use App\Models\Amenity;
use Illuminate\Support\Facades\Storage;

class TransportController extends Controller
{
    public function index(TransportDataTable $dataTable)
    {
        return $dataTable->render('dashboard.transports.index');
    }

    public function create()
    {
        $amenities = Amenity::orderByTranslation('name')->get();
        $transportTypes = [
            'bus' => 'Bus',
            'train' => 'Train',
            'ferry' => 'Ferry',
            'plane' => 'Plane',
            'car' => 'Car',
            'van' => 'Van',
            'boat' => 'Boat',
            'helicopter' => 'Helicopter',
        ];
        
        $vehicleTypes = [
            'sedan' => 'Sedan',
            'suv' => 'SUV',
            'van' => 'Van',
            'bus' => 'Bus',
            'train' => 'Train',
            'boat' => 'Boat',
            'plane' => 'Plane',
            'helicopter' => 'Helicopter',
            'limousine' => 'Limousine',
            'motorcycle' => 'Motorcycle',
        ];
        
        $routeTypes = [
            'direct' => 'Direct',
            'with_stops' => 'With Stops',
            'circular' => 'Circular',
        ];
        
        $currencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'EGP' => 'Egyptian Pound',
            'AED' => 'UAE Dirham',
            'SAR' => 'Saudi Riyal',
            'QAR' => 'Qatari Riyal',
            'KWD' => 'Kuwaiti Dinar',
        ];
        
        return view('dashboard.transports.create', compact('amenities', 'transportTypes', 'vehicleTypes', 'routeTypes', 'currencies'));
    }

    public function store(TransportRequest $request)
    {
        // Normalize amenities
        if ($request->has('amenities')) {
            $amenities = $request->input('amenities');
            if (is_string($amenities)) {
                $decoded = json_decode($amenities, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $amenities = $decoded;
                } else {
                    $amenities = array_filter(array_map('trim', preg_split('/[,\n\r\t]+/', $amenities)));
                }
            }
            if (!is_array($amenities)) {
                $amenities = [$amenities];
            }
            $request->merge(['amenities' => array_values(array_unique($amenities))]);
        }

        $data = $request->getSanitized();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('transports/featured', 'public');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('transports/gallery', 'public');
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('vehicle_images')) {
            $vehicleImages = [];
            foreach ($request->file('vehicle_images') as $image) {
                $vehicleImages[] = $image->store('transports/vehicles', 'public');
            }
            $data['vehicle_images'] = $vehicleImages;
        }

        // Get the main locale name for slug generation
        $mainLocale = config('app.locale');
        $mainName = $request->input($mainLocale . '.name');
        
        // If no name is provided, try to get from any available translation
        if (empty($mainName)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $name = $request->input($locale . '.name');
                if (!empty($name)) {
                    $mainName = $name;
                    break;
                }
            }
        }
        
        // If still no name, use a default
        if (empty($mainName)) {
            $mainName = 'Transport ' . time();
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Transport::generateSlug($mainName);
        }
        
        $transport = Transport::create($data);

        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $transport->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $transport->save();

        // Handle relationships
        $transport->amenities()->attach($request->get('amenities', []));
        $transport->seo()->create($request->get('seo', []));

        session()->flash('message', 'Transport created successfully.');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.transports.index');
    }

    public function show(Transport $transport)
    {
        $transport->load(['amenities', 'bookings']);
        return view('dashboard.transports.show', compact('transport'));
    }

    public function edit(Transport $transport)
    {
        $transport->load('amenities');
        $amenities = Amenity::orderByTranslation('name')->get();
        $transportTypes = [
            'bus' => 'Bus',
            'train' => 'Train',
            'ferry' => 'Ferry',
            'plane' => 'Plane',
            'car' => 'Car',
            'van' => 'Van',
            'boat' => 'Boat',
            'helicopter' => 'Helicopter',
        ];
        
        $vehicleTypes = [
            'sedan' => 'Sedan',
            'suv' => 'SUV',
            'van' => 'Van',
            'bus' => 'Bus',
            'train' => 'Train',
            'boat' => 'Boat',
            'plane' => 'Plane',
            'helicopter' => 'Helicopter',
            'limousine' => 'Limousine',
            'motorcycle' => 'Motorcycle',
        ];
        
        $routeTypes = [
            'direct' => 'Direct',
            'with_stops' => 'With Stops',
            'circular' => 'Circular',
        ];
        
        $currencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'EGP' => 'Egyptian Pound',
            'AED' => 'UAE Dirham',
            'SAR' => 'Saudi Riyal',
            'QAR' => 'Qatari Riyal',
            'KWD' => 'Kuwaiti Dinar',
        ];
        
        return view('dashboard.transports.edit', compact('transport', 'amenities', 'transportTypes', 'vehicleTypes', 'routeTypes', 'currencies'));
    }

    public function update(TransportRequest $request, Transport $transport)
    {
        // Normalize amenities
        if ($request->has('amenities')) {
            $amenities = $request->input('amenities');
            if (is_string($amenities)) {
                $decoded = json_decode($amenities, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $amenities = $decoded;
                } else {
                    $amenities = array_filter(array_map('trim', preg_split('/[,\n\r\t]+/', $amenities)));
                }
            }
            if (!is_array($amenities)) {
                $amenities = [$amenities];
            }
            $request->merge(['amenities' => array_values(array_unique($amenities))]);
        }

        $data = $request->getSanitized();

        if ($request->hasFile('featured_image')) {
            if ($transport->featured_image) {
                Storage::disk('public')->delete($transport->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('transports/featured', 'public');
        }

        if ($request->hasFile('images')) {
            if ($transport->images) {
                foreach ($transport->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('transports/gallery', 'public');
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('vehicle_images')) {
            if ($transport->vehicle_images) {
                foreach ($transport->vehicle_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $vehicleImages = [];
            foreach ($request->file('vehicle_images') as $image) {
                $vehicleImages[] = $image->store('transports/vehicles', 'public');
            }
            $data['vehicle_images'] = $vehicleImages;
        }

        // Get the main locale name for slug generation
        $mainLocale = config('app.locale');
        $mainName = $request->input($mainLocale . '.name');
        
        // If no name is provided, try to get from any available translation
        if (empty($mainName)) {
            foreach (config('translatable.supported_locales') as $locale => $localeName) {
                $name = $request->input($locale . '.name');
                if (!empty($name)) {
                    $mainName = $name;
                    break;
                }
            }
        }
        
        // If still no name, use existing name or default
        if (empty($mainName)) {
            $mainName = $transport->name ?? 'Transport ' . time();
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Transport::generateSlug($mainName, $transport->id);
        }
        
        $transport->update($data);

        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $transport->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $transport->save();

        // Handle relationships
        $transport->amenities()->sync($request->get('amenities', []));
        $transport->seo ?
            $transport->seo->update($request->get('seo', [])) :
            $transport->seo()->create($request->get('seo', []));

        session()->flash('message', 'Transport updated successfully.');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.transports.index');
    }

    public function destroy(Transport $transport)
    {
        // Delete associated files
        if ($transport->featured_image) {
            Storage::disk('public')->delete($transport->featured_image);
        }
        
        if ($transport->images) {
            foreach ($transport->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        if ($transport->vehicle_images) {
            foreach ($transport->vehicle_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $transport->delete();

        return response()->json([
            'message' => 'Transport deleted successfully!'
        ]);
    }
}
