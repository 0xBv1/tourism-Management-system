<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Http\Requests\Supplier\ProfileUpdateRequest;

class ProfileController extends Controller
{

    /**
     * Display the supplier profile.
     */
    public function show()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        return view('dashboard.supplier.profile.show', compact('supplier'));
    }

    /**
     * Show the form for creating a new supplier profile.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->supplier) {
            return redirect()->route('supplier.profile.show')
                ->with('info', 'You already have a supplier profile.');
        }

        return view('dashboard.supplier.profile.create');
    }

    /**
     * Store a newly created supplier profile.
     */
    public function store(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        
        if ($user->supplier) {
            return redirect()->route('supplier.profile.show')
                ->with('error', 'You already have a supplier profile.');
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        // Handle logo (media component sends the path directly)
        if ($request->has('logo')) {
            $data['logo'] = $request->logo;
        }

        $supplier = Supplier::create($data);

        // Assign supplier role if not already assigned
        if (!$user->hasRole('Supplier')) {
            $user->assignRole('Supplier');
        }

        return redirect()->route('supplier.dashboard')
            ->with('message', 'Supplier profile created successfully!');
    }

    /**
     * Show the form for editing the supplier profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        return view('dashboard.supplier.profile.edit', compact('supplier'));
    }

    /**
     * Update the supplier profile.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        $data = $request->validated();

        // Handle logo (media component sends the path directly)
        if ($request->has('logo') && $request->logo !== $supplier->logo) {
            // Delete old logo if it exists and is different
            if ($supplier->logo && $supplier->logo !== $request->logo) {
                Storage::disk('public')->delete($supplier->logo);
            }
            $data['logo'] = $request->logo;
        }

        $supplier->update($data);

        return redirect()->route('supplier.profile.show')
                ->with('message', 'Profile updated successfully!');
    }

    /**
     * Remove the supplier profile.
     */
    public function destroy()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        // Delete logo file
        if ($supplier->logo) {
            Storage::disk('public')->delete($supplier->logo);
        }

        $supplier->delete();

        // Remove supplier role
        $user->removeRole('Supplier');

        return response()->json([
            'message' => 'Supplier Profile Deleted Successfully!'
        ]);
    }
}
