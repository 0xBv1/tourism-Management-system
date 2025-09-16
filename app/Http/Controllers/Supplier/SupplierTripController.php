<?php

namespace App\Http\Controllers\Supplier;

use App\DataTables\SupplierTripDataTable;
use App\DataTables\SupplierTripBookingDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SupplierTripRequest;
use App\Models\SupplierTrip;
use App\Models\SupplierTripSeat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierTripController extends Controller
{
    public function index(SupplierTripDataTable $dataTable)
    {
        $this->authorize('supplier.trips.list');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        return $dataTable->render('dashboard.supplier.trips.index', compact('supplier'));
    }

    public function create()
    {
        $this->authorize('supplier.trips.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        $tripTypes = [
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
        ];

        $cities = \App\Models\City::orderBy('name')->pluck('name', 'name')->toArray();
        
        $amenities = [
            'WiFi' => 'WiFi',
            'Air Conditioning' => 'Air Conditioning',
            'Restroom' => 'Restroom',
            'Refreshments' => 'Refreshments',
            'Entertainment' => 'Entertainment',
            'Reclining Seats' => 'Reclining Seats',
            'Meal Included' => 'Meal Included',
        ];

        return view('dashboard.supplier.trips.create', compact('supplier', 'tripTypes', 'cities', 'amenities'));
    }

    public function store(SupplierTripRequest $request)
    {
        $this->authorize('supplier.trips.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        // Normalize amenities to array if sent as JSON or comma-separated string
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
        $data['supplier_id'] = $supplier->id;
        $data['available_seats'] = $data['total_seats'];

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('supplier/trips/featured', 'public');
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('supplier/trips/gallery', 'public');
            }
            $data['images'] = $images;
        }

        $trip = SupplierTrip::create($data);

        session()->flash('message', 'Trip created successfully! It will be reviewed by admin before approval.');
        session()->flash('type', 'success');

        return redirect()->route('supplier.trips.index');
    }

    public function show(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.view');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        return view('dashboard.supplier.trips.show', compact('supplier', 'trip'));
    }

    public function edit(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        $tripTypes = [
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
        ];

        $cities = \App\Models\City::orderBy('name')->pluck('name', 'name')->toArray();
        
        $amenities = [
            'WiFi' => 'WiFi',
            'Air Conditioning' => 'Air Conditioning',
            'Restroom' => 'Restroom',
            'Refreshments' => 'Refreshments',
            'Entertainment' => 'Entertainment',
            'Reclining Seats' => 'Reclining Seats',
            'Meal Included' => 'Meal Included',
        ];

        return view('dashboard.supplier.trips.edit', compact('supplier', 'trip', 'tripTypes', 'cities', 'amenities'));
    }

    public function update(SupplierTripRequest $request, SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        // Normalize amenities to array if sent as JSON or comma-separated string
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

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            if ($trip->featured_image) {
                Storage::disk('public')->delete($trip->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('supplier/trips/featured', 'public');
        }

        // Handle multiple images
        if ($request->hasFile('images')) {
            if ($trip->images) {
                foreach ($trip->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('supplier/trips/gallery', 'public');
            }
            $data['images'] = $images;
        }

        $trip->update($data);

        session()->flash('message', 'Trip updated successfully!');
        session()->flash('type', 'success');

        return redirect()->route('supplier.trips.index');
    }

    public function destroy(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.delete');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Delete images
        if ($trip->featured_image) {
            Storage::disk('public')->delete($trip->featured_image);
        }
        if ($trip->images) {
            foreach ($trip->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $trip->delete();

        return response()->json([
            'message' => 'Trip deleted successfully!'
        ]);
    }

    public function toggleStatus(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $trip->update(['enabled' => !$trip->enabled]);

        return response()->json([
            'success' => true,
            'message' => 'Trip status updated successfully!',
            'enabled' => $trip->enabled
        ]);
    }

    public function bookings(SupplierTrip $trip, SupplierTripBookingDataTable $dataTable)
    {
        $this->authorize('supplier.trips.view');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return $dataTable->render('dashboard.supplier.trips.bookings', compact('trip', 'supplier'));
    }

    public function details(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.view');

        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        return response()->json([
            'success' => true,
            'trip' => [
                'id' => $trip->id,
                'trip_name' => $trip->trip_name,
                'departure_city' => $trip->departure_city,
                'arrival_city' => $trip->arrival_city,
                'travel_date' => optional($trip->travel_date)->format('M d, Y'),
                'departure_time' => $trip->formatted_departure_time,
                'arrival_time' => $trip->formatted_arrival_time,
                'seat_price' => (float) $trip->seat_price,
                'available_seats' => (int) $trip->available_seats,
                'trip_type_label' => $trip->trip_type_label,
                'amenities' => $trip->amenities ?? [],
                'additional_notes' => $trip->additional_notes,
            ]
        ]);
    }

    public function seats(SupplierTrip $trip)
    {
        $this->authorize('supplier.trips.view');

        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $trip->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $seats = $trip->getAllSeats()->map(function (SupplierTripSeat $seat) {
            return [
                'seat_number' => (int) $seat->seat_number,
                'is_available' => (bool) $seat->is_available,
                'status_label' => $seat->status_label,
                'status_color' => $seat->status_color,
            ];
        });

        return response()->json([
            'success' => true,
            'seats' => $seats,
        ]);
    }
}
