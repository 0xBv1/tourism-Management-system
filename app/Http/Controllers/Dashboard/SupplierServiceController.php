<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\SupplierServicesDataTable;
use App\Http\Controllers\Controller;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\SupplierRoom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierServiceController extends Controller
{
    /**
     * Display a listing of all supplier services.
     */
    public function index(SupplierServicesDataTable $dataTable)
    {
        // Check permission
        if (!admin()->can('supplier-services.list')) {
            abort(403, 'Unauthorized action.');
        }

        // Get statistics
        $stats = $this->getServiceStatistics();

        return $dataTable->render('dashboard.supplier-services.index', compact('stats'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($type, $id)
    {
        // Check permission
        if (!admin()->can('supplier-services.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $service = $this->getService($type, $id);

        if (!$service) {
            return redirect()->route('dashboard.supplier-services.index')
                ->with('error', 'Service not found.');
        }

        return view('dashboard.supplier-services.edit', compact('service', 'type'));
    }

    /**
     * Update the specified service status.
     */
    public function update(Request $request, $type, $id)
    {
        // Check permission based on action
        $action = $request->approval_status;
        if ($action === 'approved' && !admin()->can('supplier-services.approve')) {
            abort(403, 'Unauthorized action.');
        }
        if ($action === 'rejected' && !admin()->can('supplier-services.reject')) {
            abort(403, 'Unauthorized action.');
        }
        if (!admin()->can('supplier-services.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $service = $this->getService($type, $id);

        if (!$service) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
            }
            return redirect()->route('dashboard.supplier-services.index')
                ->with('error', 'Service not found.');
        }

        $request->validate([
            'approval_status' => 'required|in:approved,pending,rejected',
            'enabled' => 'boolean',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        // Validate rejection reason when status is rejected
        if ($request->approval_status === 'rejected' && empty($request->rejection_reason)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Rejection reason is required when rejecting a service.'], 422);
            }
            return redirect()->back()->withErrors(['rejection_reason' => 'Rejection reason is required when rejecting a service.'])->withInput();
        }

        // Determine approved status based on approval_status
        $approved = $request->approval_status === 'approved';
        $rejectionReason = $request->approval_status === 'rejected' ? $request->rejection_reason : null;

        // Clear rejection reason if service is approved
        if ($approved) {
            $rejectionReason = null;
        }

        $service->update([
            'approved' => $approved,
            'enabled' => $request->enabled,
            'rejection_reason' => $rejectionReason,
        ]);

        $status = $request->approval_status;
        $message = "Service has been {$status} successfully.";

        if ($request->expectsJson()) {
            // Get updated service data
            $updatedService = $this->getService($type, $id);
            $stats = $this->getServiceStatistics();
            
            return response()->json([
                'success' => true, 
                'message' => $message,
                'service' => [
                    'id' => $updatedService->id,
                    'approved' => $updatedService->approved,
                    'enabled' => $updatedService->enabled,
                    'rejection_reason' => $updatedService->rejection_reason
                ],
                'stats' => $stats
            ]);
        }

        return redirect()->route('dashboard.supplier-services.index')
            ->with('success', $message);
    }

    /**
     * Get service statistics.
     */
    private function getServiceStatistics()
    {
        return [
            'total_services' => SupplierHotel::count() + SupplierTour::count() + SupplierTrip::count() + SupplierTransport::count() + SupplierRoom::count(),
            'total_hotels' => SupplierHotel::count(),
            'total_tours' => SupplierTour::count(),
            'total_trips' => SupplierTrip::count(),
            'total_transports' => SupplierTransport::count(),
            'total_rooms' => SupplierRoom::count(),
            'approved_services' => SupplierHotel::where('approved', true)->count() + 
                                  SupplierTour::where('approved', true)->count() + 
                                  SupplierTrip::where('approved', true)->count() + 
                                  SupplierTransport::where('approved', true)->count() +
                                  SupplierRoom::where('approved', true)->count(),
            'pending_services' => SupplierHotel::where('approved', false)->count() + 
                                 SupplierTour::where('approved', false)->count() + 
                                 SupplierTrip::where('approved', false)->count() + 
                                 SupplierTransport::where('approved', false)->count() +
                                 SupplierRoom::where('approved', false)->count(),
            'enabled_services' => SupplierHotel::where('enabled', true)->count() + 
                                 SupplierTour::where('enabled', true)->count() + 
                                 SupplierTrip::where('enabled', true)->count() + 
                                 SupplierTransport::where('enabled', true)->count() +
                                 SupplierRoom::where('enabled', true)->count(),
            'disabled_services' => SupplierHotel::where('enabled', false)->count() + 
                                  SupplierTour::where('enabled', false)->count() + 
                                  SupplierTrip::where('enabled', false)->count() + 
                                  SupplierTransport::where('enabled', false)->count() +
                                  SupplierRoom::where('enabled', false)->count(),
        ];
    }

    /**
     * Get service by type and ID.
     */
    private function getService($type, $id)
    {
        return match($type) {
            'hotel' => SupplierHotel::with('supplier.user')->find($id),
            'tour' => SupplierTour::with('supplier.user')->find($id),
            'trip' => SupplierTrip::with('supplier.user')->find($id),
            'transport' => SupplierTransport::with('supplier.user')->find($id),
            'room' => SupplierRoom::with('supplier.user')->find($id),
            default => null
        };
    }
}



