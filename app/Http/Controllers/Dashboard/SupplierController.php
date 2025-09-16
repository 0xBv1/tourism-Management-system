<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\SupplierDataTable;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Dashboard\SupplierRequest;

class SupplierController extends Controller
{

    /**
     * Display a listing of suppliers.
     */
    public function index(SupplierDataTable $dataTable)
    {
        $this->authorize('suppliers.list');

        return $dataTable->render('dashboard.suppliers.index');
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        $this->authorize('suppliers.create');

        return view('dashboard.suppliers.create');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(SupplierRequest $request)
    {
        $this->authorize('suppliers.create');

        $data = $request->validated();

        // Create user account
        $user = User::create([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['user_phone'],
        ]);

        // Assign supplier role
        $user->assignRole('Supplier');

        // Create supplier profile
        $supplier = Supplier::create([
            'user_id' => $user->id,
            'company_name' => $data['company_name'],
            'company_email' => $data['company_email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'payment_info' => $data['payment_info'] ?? null,
            'commission_rate' => $data['commission_rate'] ?? 10.00,
            'description' => $data['description'] ?? null,
            'website' => $data['website'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'business_license' => $data['business_license'] ?? null,
            'is_verified' => $data['is_verified'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if ($data['is_verified'] ?? false) {
            $supplier->markAsVerified();
        }

        return redirect()->route('dashboard.suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('suppliers.list');

        $supplier->load(['user', 'hotels', 'trips', 'tours', 'transports']);

        // Get statistics
        $stats = $this->getSupplierStats($supplier);

        return view('dashboard.suppliers.show', compact('supplier', 'stats'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        $this->authorize('supplier.profile.edit');

        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->authorize('supplier.profile.edit');

        $data = $request->validated();

        // Update user account
        $supplier->user->update([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'phone' => $data['user_phone'],
        ]);

        // Update password if provided
        if (!empty($data['password'])) {
            $supplier->user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        // Update supplier profile
        $supplier->update([
            'company_name' => $data['company_name'],
            'company_email' => $data['company_email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'payment_info' => $data['payment_info'] ?? null,
            'commission_rate' => $data['commission_rate'] ?? 10.00,
            'description' => $data['description'] ?? null,
            'website' => $data['website'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'business_license' => $data['business_license'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        // Handle verification
        if ($data['is_verified'] ?? false) {
            if (!$supplier->is_verified) {
                $supplier->markAsVerified();
            }
        } else {
            $supplier->update([
                'is_verified' => false,
                'verified_at' => null,
            ]);
        }

        return redirect()->route('dashboard.suppliers.index')
            ->with('success', 'Supplier Updated Successfully!');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('suppliers.delete');

        // Remove supplier role from user
        $supplier->user->removeRole('Supplier');

        // Delete supplier profile (this will cascade to related data)
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier Deleted Successfully!'
        ]);
    }

    /**
     * Toggle supplier verification status.
     */
    public function toggleVerification(Supplier $supplier)
    {
        $this->authorize('suppliers.edit');

        if ($supplier->is_verified) {
            $supplier->update([
                'is_verified' => false,
                'verified_at' => null,
            ]);
            $message = 'Supplier unverified successfully!';
        } else {
            $supplier->markAsVerified();
            $message = 'Supplier verified successfully!';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle supplier active status.
     */
    public function toggleActive(Supplier $supplier)
    {
        $this->authorize('suppliers.edit');

        $supplier->update([
            'is_active' => !$supplier->is_active,
        ]);

        $status = $supplier->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Supplier {$status} successfully!");
    }

    /**
     * Update supplier commission rate.
     */
    public function updateCommission(Request $request, Supplier $supplier)
    {
        $this->authorize('suppliers.edit');

        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $supplier->update([
            'commission_rate' => $request->commission_rate,
        ]);

        return redirect()->back()->with('success', 'Commission rate updated successfully!');
    }

    /**
     * Get supplier statistics.
     */
    private function getSupplierStats(Supplier $supplier)
    {
        return [
            'total_services' => $supplier->hotels()->count() + $supplier->trips()->count() + 
                               $supplier->tours()->count() + $supplier->transports()->count(),
            'pending_approvals' => $supplier->hotels()->where('approved', false)->count() + 
                                  $supplier->trips()->where('approved', false)->count() + 
                                  $supplier->tours()->where('approved', false)->count() + 
                                  $supplier->transports()->where('approved', false)->count(),
            'wallet_balance' => $supplier->wallet_balance,
            'commission_rate' => $supplier->commission_rate,
        ];
    }
}
