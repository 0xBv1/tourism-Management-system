<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Models\SettlementItem;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Dahabia;
use App\Models\Restaurant;
use App\Models\Ticket;
use App\Models\Extra;
use App\Models\ResourceBooking;
use App\Models\InquiryResource;
use App\Enums\SettlementStatus;
use App\Enums\SettlementType;
use App\Enums\CommissionType;
use App\Enums\PaymentMethod;
use App\Services\SettlementService;
use App\DataTables\SettlementDataTable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettlementController extends Controller
{
    protected $settlementService;

    public function __construct(SettlementService $settlementService)
    {
        $this->settlementService = $settlementService;
    }

    /**
     * Display a listing of settlements
     */
    public function index(Request $request, SettlementDataTable $dataTable)
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('dashboard.settlements.index');
    }

    /**
     * Show the form for creating a new settlement
     */
    public function create(): View
    {
        $guides = Guide::active()->get();
        $representatives = Representative::active()->get();
        $hotels = Hotel::active()->get();
        $vehicles = Vehicle::active()->get();
        $dahabias = Dahabia::active()->get();
        $restaurants = Restaurant::active()->get();
        $tickets = Ticket::active()->get();
        $extras = Extra::active()->get();
        // dd($guides, $representatives, $hotels, $vehicles, $dahabias, $restaurants, $tickets, $extras);
        return view('dashboard.settlements.create', compact(
            'guides', 'representatives', 'hotels', 'vehicles', 
            'dahabias', 'restaurants', 'tickets', 'extras'
        ));
    }

    /**
     * Store a newly created settlement
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'resource_type' => 'required|in:guide,representative,hotel,vehicle,dahabia,restaurant,ticket,extra',
            'resource_id' => 'required|integer',
            'settlement_type' => 'required|in:monthly,weekly,quarterly,yearly,custom',
            'month' => 'required_if:settlement_type,monthly|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2030',
            'start_date' => 'required_if:settlement_type,custom|date',
            'end_date' => 'required_if:settlement_type,custom|date|after:start_date',
            'commission_type' => 'required|in:percentage,fixed,none',
            'commission_value' => 'required_if:commission_type,percentage,fixed|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $settlement = $this->settlementService->createSettlement($request->all());

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Settlement created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating settlement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified settlement
     */
    public function show(Settlement $settlement): View
    {
        $settlement->load([
            'resource',
            'settlementItems.resourceBooking.bookingFile.inquiry',
            'calculatedBy',
            'approvedBy',
            'paidBy',
            'rejectedBy'
        ]);

        return view('dashboard.settlements.show', compact('settlement'));
    }

    /**
     * Show the form for editing the specified settlement
     */
    public function edit(Settlement $settlement): View
    {
        $guides = Guide::active()->get();
        $representatives = Representative::active()->get();

        return view('dashboard.settlements.edit', compact('settlement', 'guides', 'representatives'));
    }

    /**
     * Update the specified settlement
     */
    public function update(Request $request, Settlement $settlement): RedirectResponse
    {
        $request->validate([
            'commission_type' => 'required|in:percentage,fixed,none',
            'commission_value' => 'required_if:commission_type,percentage,fixed|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'deductions' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->settlementService->updateSettlement($settlement, $request->all());

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Settlement updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating settlement: ' . $e->getMessage());
        }
    }

    /**
     * Calculate settlement amounts
     */
    public function calculate(Settlement $settlement): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $this->settlementService->calculateSettlement($settlement);

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Settlement calculated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error calculating settlement: ' . $e->getMessage());
        }
    }

    /**
     * Approve settlement
     */
    public function approve(Request $request, Settlement $settlement): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->settlementService->approveSettlement($settlement, auth()->id(), $request->notes);

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Settlement approved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error approving settlement: ' . $e->getMessage());
        }
    }

    /**
     * Reject settlement
     */
    public function reject(Request $request, Settlement $settlement): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->settlementService->rejectSettlement($settlement, $request->rejection_reason, auth()->id());

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Settlement rejected successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error rejecting settlement: ' . $e->getMessage());
        }
    }

    /**
     * Mark settlement as paid
     */
    public function markAsPaid(Request $request, Settlement $settlement): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,check,other',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->settlementService->markSettlementAsPaid(
                $settlement, 
                auth()->id(), 
                $request->payment_method,
                $request->payment_reference,
                $request->notes
            );

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.show', $settlement)
                ->with('success', 'Payment recorded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Delete settlement
     */
    public function destroy(Settlement $settlement): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Only allow deletion if settlement is pending
            if ($settlement->status !== SettlementStatus::PENDING) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete settlement in this status');
            }

            $settlement->settlementItems()->delete();
            $settlement->delete();

            DB::commit();

            return redirect()
                ->route('dashboard.settlements.index')
                ->with('success', 'Settlement deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error deleting settlement: ' . $e->getMessage());
        }
    }

    /**
     * Generate settlements automatically for all resources
     */
    public function generateAutomatic(Request $request): RedirectResponse
    {
        $request->validate([
            'settlement_type' => 'required|in:monthly,weekly,quarterly,yearly',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'nullable|integer|between:1,12',
            'resource_type' => 'nullable|in:guide,representative,hotel,vehicle,dahabia,restaurant,ticket,extra',
            'resource_id' => 'nullable|integer|min:1',
            'commission_type' => 'nullable|in:percentage,fixed,none',
            'commission_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'deductions' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'force' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $settlementType = $request->settlement_type;
            $year = $request->year;
            $month = $request->month;
            $resourceType = $request->resource_type;
            $resourceId = $request->resource_id;
            $force = $request->boolean('force');

            // Prepare settings for settlement generation
            $settings = [
                'commission_type' => $request->commission_type ? CommissionType::from($request->commission_type) : CommissionType::PERCENTAGE,
                'commission_value' => $request->commission_value ?? 10,
                'tax_rate' => $request->tax_rate ?? 0,
                'deductions' => $request->deductions ?? 0,
                'bonuses' => $request->bonuses ?? 0,
            ];

            $settlements = [];

            if ($resourceType && $resourceId) {
                // Generate for specific resource
                $settlement = $this->settlementService->generateSettlementForSpecificResource(
                    $resourceType,
                    $resourceId,
                    $settlementType,
                    $year,
                    $month,
                    $force,
                    $settings
                );
                $settlements = $settlement ? [$settlement] : [];
            } elseif ($resourceType) {
                // Generate for specific resource type
                $settlements = $this->settlementService->generateSettlementsForResourceType(
                    $resourceType, 
                    $settlementType, 
                    $year, 
                    $month, 
                    $force,
                    $settings
                );
            } else {
                // Generate for all resources
                $settlements = $this->settlementService->generateSettlementsForAllResources(
                    $settlementType, 
                    $year, 
                    $month, 
                    $force,
                    $settings
                );
            }

            DB::commit();

            $count = count($settlements);
            $message = "Successfully generated {$count} {$settlementType} settlement" . ($count !== 1 ? 's' : '');
            
            if ($resourceType && $resourceId) {
                $message .= " for specific {$resourceType} #{$resourceId}";
            } elseif ($resourceType) {
                $message .= " for all {$resourceType}s";
            } else {
                $message .= " for all resources";
            }

            return redirect()
                ->route('dashboard.settlements.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error generating settlements: ' . $e->getMessage());
        }
    }

    /**
     * Show automatic settlement generation form
     */
    public function showGenerateForm(): View
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Get all active resources for dropdowns
        $guides = Guide::active()->get();
        $representatives = Representative::active()->get();
        $hotels = Hotel::active()->get();
        $vehicles = Vehicle::active()->get();
        $dahabias = Dahabia::active()->get();
        $restaurants = Restaurant::active()->get();
        $tickets = Ticket::active()->get();
        $extras = Extra::active()->get();
        
        return view('dashboard.settlements.generate', compact(
            'currentYear', 'currentMonth', 'guides', 'representatives', 
            'hotels', 'vehicles', 'dahabias', 'restaurants', 'tickets', 'extras'
        ));
    }

    /**
     * Get resource bookings for settlement calculation
     */
    public function getResourceBookings(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|in:guide,representative,hotel,vehicle,dahabia,restaurant,ticket,extra',
            'resource_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            // Debug: Check if the resource exists
            $resourceExists = $this->checkResourceExists($request->resource_type, $request->resource_id);
            if (!$resourceExists) {
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($request->resource_type) . ' with ID ' . $request->resource_id . ' not found or inactive',
                    'data' => null,
                    'status' => false
                ], 404);
            }

            $inquiryResources = InquiryResource::with(['inquiry'])
                ->where('resource_type', $request->resource_type)
                ->where('resource_id', $request->resource_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_at', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_at', [$request->start_date, $request->end_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_at', '<=', $request->start_date)
                                ->where('end_at', '>=', $request->end_date);
                          });
                })
                ->whereHas('inquiry', function($query) {
                    $query->whereIn('status', ['confirmed', 'in_progress', 'completed']);
                })
                ->get();

            $totalAmount = $inquiryResources->sum('effective_price');
            $totalHours = $inquiryResources->sum(function($resource) {
                if ($resource->start_at && $resource->end_at) {
                    return Carbon::parse($resource->start_at)->diffInHours(Carbon::parse($resource->end_at));
                }
                return 0;
            });
            $totalDays = $inquiryResources->sum(function($resource) {
                if ($resource->start_at && $resource->end_at) {
                    return Carbon::parse($resource->start_at)->diffInDays(Carbon::parse($resource->end_at));
                }
                return 0;
            });

            // Format inquiry resources for frontend
            $formattedBookings = $inquiryResources->map(function($resource) {
                $inquiry = $resource->inquiry;
                
                return [
                    'id' => $resource->id,
                    'booking_date' => $resource->start_at ? Carbon::parse($resource->start_at)->format('Y-m-d') : 'N/A',
                    'client_name' => $inquiry ? ($inquiry->client_name ?? 'N/A') : 'N/A',
                    'tour_name' => $inquiry ? ($inquiry->tour_name ?? 'N/A') : 'N/A',
                    'duration_text' => $this->getDurationText($resource),
                    'unit_price' => $resource->effective_price ?? 0,
                    'amount' => $resource->effective_price ?? 0,
                    'formatted_unit_price' => $resource->effective_price ? '$' . number_format($resource->effective_price, 2) : 'N/A',
                    'formatted_amount' => $resource->effective_price ? '$' . number_format($resource->effective_price, 2) : 'N/A',
                    'status' => $inquiry->status ? $inquiry->status->value : 'unknown',
                    'status_label' => ucfirst($inquiry->status ? $inquiry->status->value : 'Unknown'),
                    'status_color' => $this->getStatusColor($inquiry->status ? $inquiry->status->value : 'unknown'),
                ];
            });

            return response()->json([
                'success' => true,
                'bookings' => $formattedBookings,
                'total_amount' => $totalAmount,
                'formatted_total_amount' => '$' . number_format($totalAmount, 2),
                'total_hours' => $totalHours,
                'total_days' => $totalDays,
                'count' => $inquiryResources->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bookings: ' . $e->getMessage(),
                'bookings' => [],
                'total_amount' => 0,
                'formatted_total_amount' => '$0.00',
                'total_hours' => 0,
                'total_days' => 0,
                'count' => 0,
            ], 500);
        }
    }

    /**
     * Check if a resource exists and is active
     */
    private function checkResourceExists(string $resourceType, int $resourceId): bool
    {
        return match($resourceType) {
            'guide' => Guide::withoutGlobalScopes()->find($resourceId) !== null,
            'representative' => Representative::withoutGlobalScopes()->find($resourceId) !== null,
            'hotel' => Hotel::withoutGlobalScopes()->find($resourceId) !== null,
            'vehicle' => Vehicle::withoutGlobalScopes()->find($resourceId) !== null,
            'dahabia' => Dahabia::withoutGlobalScopes()->find($resourceId) !== null,
            'restaurant' => Restaurant::withoutGlobalScopes()->find($resourceId) !== null,
            'ticket' => Ticket::withoutGlobalScopes()->find($resourceId) !== null,
            'extra' => Extra::withoutGlobalScopes()->find($resourceId) !== null,
            default => false
        };
    }

    /**
     * Get duration text for inquiry resource
     */
    private function getDurationText($resource)
    {
        if ($resource->start_at && $resource->end_at) {
            $hours = Carbon::parse($resource->start_at)->diffInHours(Carbon::parse($resource->end_at));
            if ($hours > 24) {
                $days = Carbon::parse($resource->start_at)->diffInDays(Carbon::parse($resource->end_at));
                return $days . ' days';
            }
            return $hours . ' hours';
        }
        return 'N/A';
    }

    /**
     * Get status color for booking
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'confirmed' => 'success',
            'in_progress' => 'warning',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}