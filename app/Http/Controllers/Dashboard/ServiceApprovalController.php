<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\ServiceApprovalDataTable;
use App\Models\ServiceApproval;
use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Models\SupplierRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceApprovalController extends Controller
{
    /**
     * Display a listing of service approvals.
     */
    public function index(ServiceApprovalDataTable $dataTable)
    {
        $this->authorize('service-approvals.list');

        try {
            // Build base query for statistics
            $baseQuery = ServiceApproval::query();
            
            // Apply filters to statistics if they exist
            if (request()->filled('status')) {
                $baseQuery->where('status', request('status'));
            }
            if (request()->filled('service_type')) {
                $baseQuery->where('service_type', request('service_type'));
            }
            if (request()->filled('supplier_id')) {
                $baseQuery->where('supplier_id', request('supplier_id'));
            }
            
            // Get filtered statistics
            $stats = [
                'total' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            ];

            // Get suppliers for filter dropdown
            $suppliers = Supplier::with('user')->get();

            return $dataTable->render('dashboard.service-approvals.index', compact('stats', 'suppliers'));
        } catch (\Exception $e) {
            \Log::error('ServiceApprovalController index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading service approvals.');
        }
    }

    /**
     * Display the specified service approval.
     */
    public function show(ServiceApproval $serviceApproval)
    {
        $this->authorize('service-approvals.show');

        try {
            // Load basic relationships that are safe
            $serviceApproval->load(['supplier.user', 'approvedBy']);
            
            // Load the service relationship manually to avoid issues
            $serviceApproval->setRelation('service', $this->loadServiceRelation($serviceApproval));
            
        } catch (\Exception $e) {
            \Log::error('ServiceApprovalController show error: ' . $e->getMessage());
            return redirect()->route('dashboard.service-approvals.index')
                ->with('error', 'An error occurred while loading service approval details.');
        }

        return view('dashboard.service-approvals.show', compact('serviceApproval'));
    }

    /**
     * Approve a service.
     */
    public function approve(ServiceApproval $serviceApproval)
    {
        $this->authorize('service-approvals.approve');

        // Check if already approved
        if ($serviceApproval->isApproved()) {
            return redirect()->back()
                ->with('warning', 'Service is already approved.');
        }



        try {
            DB::beginTransaction();

            // Approve the service approval
            $serviceApproval->approve(auth()->id());

            // Update the actual service to be approved
            $this->approveService($serviceApproval->service_type, $serviceApproval->service_id);

            DB::commit();

            Log::info('Service approved', [
                'approval_id' => $serviceApproval->id,
                'service_type' => $serviceApproval->service_type,
                'service_id' => $serviceApproval->service_id,
                'approved_by' => auth()->id(),
            ]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Service approved successfully!']);
            }
            
            return redirect()->route('dashboard.service-approvals.index')
                ->with('success', 'Service approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve service', [
                'approval_id' => $serviceApproval->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to approve service. Please try again.');
        }
    }

    /**
     * Reject a service.
     */
    public function reject(Request $request, ServiceApproval $serviceApproval)
    {
        $this->authorize('service-approvals.reject');

        // Check if already rejected
        if ($serviceApproval->isRejected()) {
            return redirect()->back()
                ->with('warning', 'Service is already rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Reject the service approval
            $serviceApproval->reject(auth()->id(), $request->rejection_reason);

            // Update the actual service to be rejected
            $this->rejectService($serviceApproval->service_type, $serviceApproval->service_id);

            DB::commit();

            Log::info('Service rejected', [
                'approval_id' => $serviceApproval->id,
                'service_type' => $serviceApproval->service_type,
                'service_id' => $serviceApproval->service_id,
                'rejected_by' => auth()->id(),
                'reason' => $request->rejection_reason,
            ]);

            return redirect()->route('dashboard.service-approvals.index')
                ->with('success', 'Service rejected successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject service', [
                'approval_id' => $serviceApproval->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to reject service. Please try again.');
        }
    }

    /**
     * Approve the actual service based on type.
     */
    private function approveService(string $serviceType, int $serviceId): void
    {


        switch ($serviceType) {
            case 'hotel':
                SupplierHotel::where('id', $serviceId)->update(['approved' => true]);
                break;
            case 'tour':
                SupplierTour::where('id', $serviceId)->update(['approved' => true]);
                break;
            case 'trip':
                SupplierTrip::where('id', $serviceId)->update(['approved' => true]);
                break;
            case 'transport':
                SupplierTransport::where('id', $serviceId)->update(['approved' => true]);
                break;
            case 'room':
                SupplierRoom::where('id', $serviceId)->update(['approved' => true]);
                break;
        }
    }

    /**
     * Reject the actual service based on type.
     */
    private function rejectService(string $serviceType, int $serviceId): void
    {
        switch ($serviceType) {
            case 'hotel':
                SupplierHotel::where('id', $serviceId)->update(['approved' => false]);
                break;
            case 'tour':
                SupplierTour::where('id', $serviceId)->update(['approved' => false]);
                break;
            case 'trip':
                SupplierTrip::where('id', $serviceId)->update(['approved' => false]);
                break;
            case 'transport':
                SupplierTransport::where('id', $serviceId)->update(['approved' => false]);
                break;
            case 'room':
                SupplierRoom::where('id', $serviceId)->update(['approved' => false]);
                break;
        }
    }

    /**
     * Update the status of a service approval.
     */
    public function updateStatus(Request $request, ServiceApproval $serviceApproval)
    {
        $newStatus = $request->status;
        
        // Debug logging for permissions
        Log::info('Service approval status update attempt', [
            'user_id' => auth()->id(),
            'user_permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray(),
            'requested_status' => $newStatus,
            'can_update' => auth()->user()->can('service-approvals.update'),
            'can_approve' => auth()->user()->can('service-approvals.approve'),
            'can_reject' => auth()->user()->can('service-approvals.reject'),
        ]);
        
        // Check if user has permission to update service approvals
        if (!auth()->user()->can('service-approvals.update') && 
            !auth()->user()->can('service-approvals.approve') && 
            !auth()->user()->can('service-approvals.reject')) {
            abort(403, 'You do not have permission to update service approval status.');
        }
        
        // Check specific permissions based on the action
        if ($newStatus === 'approved') {
            if (!auth()->user()->can('service-approvals.approve')) {
                abort(403, 'You do not have permission to approve services.');
            }
        } elseif ($newStatus === 'rejected') {
            if (!auth()->user()->can('service-approvals.reject')) {
                abort(403, 'You do not have permission to reject services.');
            }
        }

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|min:10|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $serviceApproval->status;
            $newStatus = $request->status;

            // Update the service approval status
            if ($newStatus === 'approved') {
                $serviceApproval->approve(auth()->id());
                // Update the actual service to be approved
                $this->approveService($serviceApproval->service_type, $serviceApproval->service_id);
            } elseif ($newStatus === 'rejected') {
                $rejectionReason = $request->rejection_reason ?? 'Status updated via show page';
                $serviceApproval->reject(auth()->id(), $rejectionReason);
                // Update the actual service to be rejected
                $this->rejectService($serviceApproval->service_type, $serviceApproval->service_id);
            } else {
                // Reset to pending
                $serviceApproval->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);
            }

            DB::commit();

            Log::info('Service approval status updated', [
                'approval_id' => $serviceApproval->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'new_status' => $newStatus,
                'status_label' => ucfirst($newStatus),
                'status_color' => match($newStatus) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                },
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update service approval status', [
                'approval_id' => $serviceApproval->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'user_permissions' => auth()->user()->getAllPermissions()->pluck('name')->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.',
            ], 500);
        }
    }

    /**
     * Load the service relationship manually to avoid eager loading issues.
     */
    private function loadServiceRelation(ServiceApproval $serviceApproval)
    {
        try {
            switch ($serviceApproval->service_type) {
                case 'hotel':
                    return SupplierHotel::find($serviceApproval->service_id);
                case 'tour':
                    return SupplierTour::find($serviceApproval->service_id);
                case 'trip':
                    return SupplierTrip::find($serviceApproval->service_id);
                case 'transport':
                    return SupplierTransport::find($serviceApproval->service_id);
                case 'room':
                    return SupplierRoom::find($serviceApproval->service_id);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error loading service relation: ' . $e->getMessage());
            return null;
        }
    }
}
