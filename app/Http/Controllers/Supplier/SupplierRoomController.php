<?php

namespace App\Http\Controllers\Supplier;

use App\DataTables\SupplierRoomDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SupplierRoomRequest;
use App\Models\Amenity;
use App\Models\SupplierHotel;
use App\Models\SupplierRoom;
use App\Models\ServiceApproval;

class SupplierRoomController extends Controller
{
    public function index(SupplierRoomDataTable $dataTable)
    {
        $this->authorize('supplier.rooms.list');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        return $dataTable->render('dashboard.supplier.rooms.index', compact('supplier'));
    }

    public function store(SupplierRoomRequest $request)
    {
        $this->authorize('supplier.rooms.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        // Verify the hotel belongs to the supplier
        $hotel = SupplierHotel::where('id', $request->supplier_hotel_id)
            ->where('supplier_id', $supplier->id)
            ->first();

        if (!$hotel) {
            return redirect()->back()->with('error', 'Invalid hotel selected.');
        }

        // Create room with non-translatable fields
        $room = SupplierRoom::create($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $room->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $room->save();
        
        // Generate slug from the name after translations are saved
        if (empty($room->slug)) {
            $defaultLocale = config('app.locale');
            $translation = $room->translate($defaultLocale);
            if ($translation && !empty($translation->name)) {
                $room->slug = $room->generateUniqueSlug($translation->name);
                $room->save();
            }
        }
        
        // Handle relationships
        $room->amenities()->attach($request->get('amenities'));
        $room->seo()->create($request->get('seo'));
        
        // Create service approval record
        ServiceApproval::create([
            'supplier_id' => $supplier->id,
            'service_type' => 'room',
            'service_id' => $room->id,
            'status' => 'pending',
        ]);
        
        session()->flash('message', 'Room Created Successfully! It will be reviewed by admin before approval.');
        session()->flash('type', 'success');
        
        return redirect()->route('supplier.rooms.edit', $room);
    }

    public function create()
    {
        $this->authorize('supplier.rooms.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        $hotels = SupplierHotel::where('supplier_id', $supplier->id)
            ->orderByTranslation('name')
            ->get()
            ->mapWithKeys(function($hotel) {
                return [$hotel->id => $hotel->translate('en')->name];
            })->toArray();
            
        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->translate('en')->name];
        })->toArray();
        
        return view('dashboard.supplier.rooms.create', compact('hotels', 'amenities', 'supplier'));
    }

    public function show(SupplierRoom $room)
    {
        $this->authorize('supplier.rooms.view');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $room->supplierHotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        return view('dashboard.supplier.rooms.show', compact('supplier', 'room'));
    }

    public function edit(SupplierRoom $room)
    {
        $this->authorize('supplier.rooms.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $room->supplierHotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        $room->load('amenities');
        $hotels = SupplierHotel::where('supplier_id', $supplier->id)
            ->orderByTranslation('name')
            ->get()
            ->mapWithKeys(function($hotel) {
                return [$hotel->id => $hotel->translate('en')->name];
            })->toArray();
            
        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->translate('en')->name];
        })->toArray();
        
        return view('dashboard.supplier.rooms.edit', compact('room', 'amenities', 'hotels', 'supplier'));
    }

    public function update(SupplierRoomRequest $request, SupplierRoom $room)
    {
        $this->authorize('supplier.rooms.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $room->supplierHotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        // Verify the hotel belongs to the supplier
        $hotel = SupplierHotel::where('id', $request->supplier_hotel_id)
            ->where('supplier_id', $supplier->id)
            ->first();

        if (!$hotel) {
            return redirect()->back()->with('error', 'Invalid hotel selected.');
        }

        // Update non-translatable fields
        $room->update($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $room->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $room->save();
        
        // Generate slug from the name after translations are updated
        if (empty($room->slug)) {
            $defaultLocale = config('app.locale');
            $translation = $room->translate($defaultLocale);
            if ($translation && !empty($translation->name)) {
                $room->slug = $room->generateUniqueSlug($translation->name);
                $room->save();
            }
        }
        
        // Handle relationships
        $room->amenities()->sync($request->get('amenities'));
        $room->seo ?
            $room->seo->update($request->get('seo')) :
            $room->seo()->create($request->get('seo'));
        
        // Update or create service approval record if not exists
        $serviceApproval = ServiceApproval::where('service_type', 'room')
            ->where('service_id', $room->id)
            ->first();
            
        if (!$serviceApproval) {
            ServiceApproval::create([
                'supplier_id' => $supplier->id,
                'service_type' => 'room',
                'service_id' => $room->id,
                'status' => 'pending',
            ]);
        }
            
        session()->flash('message', 'Room Updated Successfully!');
        session()->flash('type', 'success');
        
        return back();
    }

    public function destroy(SupplierRoom $room)
    {
        $this->authorize('supplier.rooms.delete');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $room->supplierHotel->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Delete associated service approval record
        ServiceApproval::where('service_type', 'room')
            ->where('service_id', $room->id)
            ->delete();

        $room->delete();
        
        return response()->json([
            'message' => 'Room Deleted Successfully!'
        ]);
    }

    /**
     * Toggle room status.
     */
    public function toggleStatus(SupplierRoom $room)
    {
        $this->authorize('supplier.rooms.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $room->supplierHotel->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $room->update(['enabled' => !$room->enabled]);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully!',
            'enabled' => $room->enabled
        ]);
    }
}
