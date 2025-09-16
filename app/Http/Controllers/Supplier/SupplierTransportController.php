<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SupplierTransport;
use Illuminate\Http\Request;
use App\Http\Requests\Supplier\SupplierTransportRequest;
use App\DataTables\SupplierTransportDataTable;

class SupplierTransportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierTransportDataTable $dataTable)
    {
        // Statistics
        $stats = [
            'total' => SupplierTransport::where('supplier_id', auth()->user()->supplier->id)->count(),
            'active' => SupplierTransport::where('supplier_id', auth()->user()->supplier->id)->where('enabled', true)->count(),
            'approved' => SupplierTransport::where('supplier_id', auth()->user()->supplier->id)->where('approved', true)->count(),
            'pending' => SupplierTransport::where('supplier_id', auth()->user()->supplier->id)->where('approved', false)->count(),
        ];

        return $dataTable->render('dashboard.supplier.transports.index', compact('stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $amenities = \App\Models\Amenity::orderByTranslation('name')->get();
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
        
        return view('dashboard.supplier.transports.create', compact('amenities', 'transportTypes', 'vehicleTypes', 'routeTypes', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierTransportRequest $request)
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        // Amenities are already normalized in the request
        $data = $request->getSanitized();
        $data['supplier_id'] = $supplier->id;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('supplier/transports/featured', 'public');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('supplier/transports/gallery', 'public');
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('vehicle_images')) {
            $vehicleImages = [];
            foreach ($request->file('vehicle_images') as $image) {
                $vehicleImages[] = $image->store('supplier/transports/vehicles', 'public');
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
            $data['slug'] = \App\Models\SupplierTransport::generateSlug($mainName);
        }
        
        $transport = \App\Models\SupplierTransport::create($data);

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

        session()->flash('message', 'Transport created successfully! It will be reviewed by admin before approval.');
        session()->flash('type', 'success');

        return redirect()->route('supplier.transports.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierTransport $transport)
    {
        if ($transport->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

        return view('dashboard.supplier.transports.show', compact('transport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierTransport $transport)
    {
        if ($transport->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

        $transport->load('amenities');
        $amenities = \App\Models\Amenity::orderByTranslation('name')->get();
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
        
        return view('dashboard.supplier.transports.edit', compact('transport', 'amenities', 'transportTypes', 'vehicleTypes', 'routeTypes', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierTransportRequest $request, SupplierTransport $transport)
    {
        if ($transport->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

        // Amenities are already normalized in the request
        $data = $request->getSanitized();

        if ($request->hasFile('featured_image')) {
            if ($transport->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($transport->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('supplier/transports/featured', 'public');
        }

        if ($request->hasFile('images')) {
            if ($transport->images) {
                foreach ($transport->images as $image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('supplier/transports/gallery', 'public');
            }
            $data['images'] = $images;
        }

        if ($request->hasFile('vehicle_images')) {
            if ($transport->vehicle_images) {
                foreach ($transport->vehicle_images as $image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
                }
            }
            $vehicleImages = [];
            foreach ($request->file('vehicle_images') as $image) {
                $vehicleImages[] = $image->store('supplier/transports/vehicles', 'public');
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
            $data['slug'] = \App\Models\SupplierTransport::generateSlug($mainName, $transport->id);
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

        session()->flash('message', 'Transport updated successfully!');
        session()->flash('type', 'success');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierTransport $transport)
    {
        if ($transport->supplier_id !== auth()->user()->supplier->id) {
            abort(403);
        }

        $transport->delete();

        return response()->json([
            'message' => 'Transport deleted successfully!'
        ]);   
    }
}
