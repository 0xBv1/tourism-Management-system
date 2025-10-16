<?php

namespace App\Services;

use App\Models\Settlement;
use App\Models\SettlementItem;
use App\Models\ResourceBooking;
use App\Models\InquiryResource;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Dahabia;
use App\Models\Restaurant;
use App\Models\Ticket;
use App\Models\Extra;
use App\Enums\SettlementStatus;
use App\Enums\SettlementType;
use App\Enums\CommissionType;
use App\Enums\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    /**
     * Create a new settlement
     */
    public function createSettlement(array $data): Settlement
    {
        // Determine settlement dates based on type
        $dates = $this->calculateSettlementDates($data);
        
        $settlement = Settlement::create([
            'settlement_type' => $data['settlement_type'],
            'resource_type' => $data['resource_type'],
            'resource_id' => $data['resource_id'],
            'month' => $dates['month'],
            'year' => $dates['year'],
            'start_date' => $dates['start_date'],
            'end_date' => $dates['end_date'],
            'commission_type' => $data['commission_type'],
            'commission_value' => $data['commission_value'] ?? 0,
            'tax_rate' => $data['tax_rate'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
            'bonuses' => $data['bonuses'] ?? 0,
            'notes' => $data['notes'] ?? '',
            'status' => SettlementStatus::PENDING,
            'currency' => 'USD', // Default currency
        ]);

        return $settlement;
    }

    /**
     * Update settlement
     */
    public function updateSettlement(Settlement $settlement, array $data): void
    {
        $settlement->update([
            'commission_type' => $data['commission_type'],
            'commission_value' => $data['commission_value'] ?? 0,
            'tax_rate' => $data['tax_rate'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
            'bonuses' => $data['bonuses'] ?? 0,
            'notes' => $data['notes'] ?? '',
        ]);

        // Recalculate if settlement is already calculated
        if ($settlement->status === SettlementStatus::CALCULATED) {
            $this->calculateSettlement($settlement);
        }
    }

    /**
     * Calculate settlement amounts
     */
    public function calculateSettlement(Settlement $settlement): void
    {
        // Get resource bookings for the settlement period
        $bookings = $this->getResourceBookings($settlement);
        
        // Clear existing settlement items
        $settlement->settlementItems()->delete();
        
        $totalAmount = 0;
        $totalHours = 0;
        $totalDays = 0;
        $totalBookings = 0;

        // Create settlement items for each inquiry resource
        foreach ($bookings as $inquiryResource) {
            $durationHours = $this->calculateDurationHours($inquiryResource);
            $durationDays = $this->calculateDurationDays($inquiryResource);
            
            SettlementItem::create([
                'settlement_id' => $settlement->id,
                'resource_booking_id' => null, // Not using ResourceBooking
                'booking_file_id' => $inquiryResource->inquiry->booking_file_id,
                'booking_date' => $inquiryResource->start_at ? $inquiryResource->start_at->format('Y-m-d') : now()->format('Y-m-d'),
                'start_time' => $inquiryResource->start_at ? $inquiryResource->start_at->format('H:i:s') : null,
                'end_time' => $inquiryResource->end_at ? $inquiryResource->end_at->format('H:i:s') : null,
                'duration_hours' => $durationHours,
                'duration_days' => $durationDays,
                'unit_price' => $inquiryResource->effective_price ?? 0,
                'total_price' => $inquiryResource->effective_price ?? 0,
                'currency' => $inquiryResource->currency ?? 'USD',
                'client_name' => $inquiryResource->inquiry->guest_name ?? 'Not Specified',
                'tour_name' => $inquiryResource->inquiry->tour_name ?? 'Not Specified',
                'notes' => $inquiryResource->price_note,
            ]);

            $totalAmount += $inquiryResource->effective_price ?? 0;
            $totalHours += $durationHours;
            $totalDays += $durationDays;
            $totalBookings++;
        }

        // Calculate commission
        $commissionAmount = $this->calculateCommission($totalAmount, $settlement->commission_type, $settlement->commission_value);
        
        // Calculate tax
        $taxAmount = $this->calculateTax($totalAmount, $settlement->tax_rate);
        
        // Calculate net amount
        $netAmount = $totalAmount - $commissionAmount - $taxAmount - $settlement->deductions + $settlement->bonuses;

        // Update settlement
        $settlement->update([
            'total_bookings' => $totalBookings,
            'total_hours' => $totalHours,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'commission_amount' => $commissionAmount,
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
            'status' => SettlementStatus::CALCULATED,
            'calculated_at' => now(),
            'calculated_by' => auth()->id(),
        ]);
    }

    /**
     * Approve settlement
     */
    public function approveSettlement(Settlement $settlement, int $userId, ?string $notes = null): void
    {
        if ($settlement->status !== SettlementStatus::CALCULATED) {
            throw new \Exception('Cannot approve settlement in this status');
        }

        $settlement->update([
            'status' => SettlementStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId,
            'notes' => $notes ? $settlement->notes . "\n" . $notes : $settlement->notes,
        ]);
    }

    /**
     * Reject settlement
     */
    public function rejectSettlement(Settlement $settlement, string $reason, int $userId): void
    {
        if ($settlement->status === SettlementStatus::PAID) {
            throw new \Exception('Cannot reject paid settlement');
        }

        $settlement->update([
            'status' => SettlementStatus::REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'rejected_by' => $userId,
        ]);
    }

    /**
     * Mark settlement as paid
     */
    public function markSettlementAsPaid(Settlement $settlement, int $userId, string $paymentMethod, ?string $paymentReference = null, ?string $notes = null): void
    {
        if ($settlement->status !== SettlementStatus::APPROVED) {
            throw new \Exception('Cannot pay non-approved settlement');
        }

        $settlement->update([
            'status' => SettlementStatus::PAID,
            'paid_at' => now(),
            'paid_by' => $userId,
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
            'notes' => $notes ? $settlement->notes . "\n" . $notes : $settlement->notes,
        ]);
    }

    /**
     * Get resource bookings for settlement period
     */
    private function getResourceBookings(Settlement $settlement): \Illuminate\Database\Eloquent\Collection
    {
        return InquiryResource::with(['inquiry'])
            ->where('resource_type', $settlement->resource_type)
            ->where('resource_id', $settlement->resource_id)
            ->where(function($query) use ($settlement) {
                $query->whereBetween('start_at', [$settlement->start_date, $settlement->end_date])
                      ->orWhereBetween('end_at', [$settlement->start_date, $settlement->end_date])
                      ->orWhere(function($q) use ($settlement) {
                          $q->where('start_at', '<=', $settlement->start_date)
                            ->where('end_at', '>=', $settlement->end_date);
                      });
            })
            ->whereHas('inquiry', function($query) {
                $query->whereIn('status', ['confirmed', 'in_progress', 'completed']);
            })
            ->get();
    }

    /**
     * Calculate settlement dates based on type
     */
    private function calculateSettlementDates(array $data): array
    {
        $settlementType = $data['settlement_type'];
        $year = $data['year'];
        
        switch ($settlementType) {
            case SettlementType::MONTHLY->value:
                $month = $data['month'];
                $startDate = Carbon::create($year, $month, 1);
                $endDate = $startDate->copy()->endOfMonth();
                break;
                
            case SettlementType::WEEKLY->value:
                $startDate = Carbon::create($year, 1, 1)->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
                break;
                
            case SettlementType::QUARTERLY->value:
                $quarter = ceil($data['month'] / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate = Carbon::create($year, $startMonth, 1);
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                break;
                
            case SettlementType::YEARLY->value:
                $startDate = Carbon::create($year, 1, 1);
                $endDate = Carbon::create($year, 12, 31);
                break;
                
            case SettlementType::CUSTOM->value:
                $startDate = Carbon::parse($data['start_date']);
                $endDate = Carbon::parse($data['end_date']);
                break;
                
            default:
                throw new \Exception('Invalid settlement type');
        }

        return [
            'month' => $startDate->month,
            'year' => $year,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ];
    }

    /**
     * Calculate duration in hours for inquiry resource
     */
    private function calculateDurationHours($inquiryResource): float
    {
        if ($inquiryResource->start_at && $inquiryResource->end_at) {
            return $inquiryResource->start_at->diffInHours($inquiryResource->end_at);
        }
        return 0;
    }

    /**
     * Calculate duration in days for inquiry resource
     */
    private function calculateDurationDays($inquiryResource): float
    {
        if ($inquiryResource->start_at && $inquiryResource->end_at) {
            return $inquiryResource->start_at->diffInDays($inquiryResource->end_at) + 1; // Include both start and end days
        }
        return 0;
    }

    /**
     * Calculate commission amount
     */
    private function calculateCommission(float $totalAmount, CommissionType $commissionType, float $commissionValue): float
    {
        switch ($commissionType) {
            case CommissionType::PERCENTAGE:
                return ($totalAmount * $commissionValue) / 100;
            case CommissionType::FIXED:
                return $commissionValue;
            case CommissionType::NONE:
            default:
                return 0;
        }
    }

    /**
     * Calculate tax amount
     */
    private function calculateTax(float $totalAmount, float $taxRate): float
    {
        return ($totalAmount * $taxRate) / 100;
    }

    /**
     * Generate monthly settlements for all active resources
     */
    public function generateMonthlySettlements(int $month, int $year, array $settings = []): array
    {
        $settlements = [];
        
        // Get all active resources
        $guides = Guide::active()->get();
        $representatives = Representative::active()->get();
        $hotels = Hotel::active()->get();
        $vehicles = Vehicle::active()->get();
        $dahabias = Dahabia::active()->get();
        $restaurants = Restaurant::active()->get();
        $tickets = Ticket::active()->get();
        $extras = Extra::active()->get();
        
        $resources = collect()
            ->concat($guides->map(fn($r) => ['resource' => $r, 'type' => 'guide']))
            ->concat($representatives->map(fn($r) => ['resource' => $r, 'type' => 'representative']))
            ->concat($hotels->map(fn($r) => ['resource' => $r, 'type' => 'hotel']))
            ->concat($vehicles->map(fn($r) => ['resource' => $r, 'type' => 'vehicle']))
            ->concat($dahabias->map(fn($r) => ['resource' => $r, 'type' => 'dahabia']))
            ->concat($restaurants->map(fn($r) => ['resource' => $r, 'type' => 'restaurant']))
            ->concat($tickets->map(fn($r) => ['resource' => $r, 'type' => 'ticket']))
            ->concat($extras->map(fn($r) => ['resource' => $r, 'type' => 'extra']));
        
        foreach ($resources as $resourceData) {
            $resource = $resourceData['resource'];
            $resourceType = $resourceData['type'];
            
            // Check if settlement already exists
            $existingSettlement = Settlement::where('resource_type', $resourceType)
                ->where('resource_id', $resource->id)
                ->where('month', $month)
                ->where('year', $year)
                ->where('settlement_type', SettlementType::MONTHLY)
                ->first();
                
            if ($existingSettlement) {
                continue; // Skip if already exists
            }
            
            // Create settlement
            $settlementData = [
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
                'settlement_type' => SettlementType::MONTHLY,
                'month' => $month,
                'year' => $year,
                'commission_type' => $settings['commission_type'] ?? CommissionType::PERCENTAGE,
                'commission_value' => $settings['commission_value'] ?? 10,
                'tax_rate' => $settings['tax_rate'] ?? 0,
                'deductions' => $settings['deductions'] ?? 0,
                'bonuses' => $settings['bonuses'] ?? 0,
                'notes' => 'Automatic monthly settlement',
            ];
            
            $settlement = $this->createSettlement($settlementData);
            // Don't automatically calculate - keep as pending for manual review
            
            $settlements[] = $settlement;
        }
        
        return $settlements;
    }

    /**
     * Find existing settlement
     */
    public function findExistingSettlement(array $settlementData): ?Settlement
    {
        $query = Settlement::where('resource_type', $settlementData['resource_type'])
            ->where('resource_id', $settlementData['resource_id'])
            ->where('settlement_type', $settlementData['settlement_type'])
            ->where('year', $settlementData['year']);

        if (isset($settlementData['month'])) {
            $query->where('month', $settlementData['month']);
        }

        if (isset($settlementData['start_date']) && isset($settlementData['end_date'])) {
            $query->where('start_date', $settlementData['start_date'])
                  ->where('end_date', $settlementData['end_date']);
        }

        return $query->first();
    }

    /**
     * Generate settlements for all resources of a specific type
     */
    public function generateSettlementsForResourceType(string $resourceType, string $settlementType, int $year, ?int $month = null, bool $force = false, array $settings = []): array
    {
        $resources = $this->getResourcesByType($resourceType);
        $settlements = [];

        foreach ($resources as $resource) {
            $settlementData = [
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
                'settlement_type' => $settlementType,
                'year' => $year,
                'commission_type' => $settings['commission_type'] ?? CommissionType::PERCENTAGE,
                'commission_value' => $settings['commission_value'] ?? 10,
                'tax_rate' => $settings['tax_rate'] ?? 0,
                'deductions' => $settings['deductions'] ?? 0,
                'bonuses' => $settings['bonuses'] ?? 0,
                'notes' => "Automatic {$settlementType} settlement generated on " . Carbon::now()->format('Y-m-d H:i:s'),
            ];

            if ($month) {
                $settlementData['month'] = $month;
            }

            // Check if settlement already exists
            if (!$force) {
                $existingSettlement = $this->findExistingSettlement($settlementData);
                if ($existingSettlement) {
                    continue;
                }
            }

            $settlement = $this->createSettlement($settlementData);
            // Don't automatically calculate - keep as pending for manual review
            $settlements[] = $settlement;
        }

        return $settlements;
    }

    /**
     * Get resources by type
     */
    private function getResourcesByType(string $resourceType)
    {
        return match($resourceType) {
            'guide' => Guide::active()->get(),
            'representative' => Representative::active()->get(),
            'hotel' => Hotel::active()->get(),
            'vehicle' => Vehicle::active()->get(),
            'dahabia' => Dahabia::active()->get(),
            'restaurant' => Restaurant::active()->get(),
            'ticket' => Ticket::active()->get(),
            'extra' => Extra::active()->get(),
            default => collect()
        };
    }

    /**
     * Generate settlements for all active resources
     */
    public function generateSettlementsForAllResources(string $settlementType, int $year, ?int $month = null, bool $force = false, array $settings = []): array
    {
        $resourceTypes = ['guide', 'representative', 'hotel', 'vehicle', 'dahabia', 'restaurant', 'ticket', 'extra'];
        $allSettlements = [];

        foreach ($resourceTypes as $resourceType) {
            $settlements = $this->generateSettlementsForResourceType($resourceType, $settlementType, $year, $month, $force, $settings);
            $allSettlements = array_merge($allSettlements, $settlements);
        }

        return $allSettlements;
    }

    /**
     * Generate weekly settlements for all resources
     */
    public function generateWeeklySettlements(int $year, ?int $month = null, bool $force = false): array
    {
        return $this->generateSettlementsForAllResources('weekly', $year, $month, $force);
    }

    /**
     * Generate quarterly settlements for all resources
     */
    public function generateQuarterlySettlements(int $year, ?int $month = null, bool $force = false): array
    {
        return $this->generateSettlementsForAllResources('quarterly', $year, $month, $force);
    }

    /**
     * Generate yearly settlements for all resources
     */
    public function generateYearlySettlements(int $year, ?int $month = null, bool $force = false): array
    {
        return $this->generateSettlementsForAllResources('yearly', $year, $month, $force);
    }

    /**
     * Generate settlement for a specific resource
     */
    public function generateSettlementForSpecificResource(string $resourceType, int $resourceId, string $settlementType, int $year, ?int $month = null, bool $force = false, array $settings = []): ?Settlement
    {
        // Check if the resource exists and is active
        $resource = $this->getResourceById($resourceType, $resourceId);
        if (!$resource) {
            throw new \Exception("Resource not found or inactive: {$resourceType} #{$resourceId}");
        }

        $settlementData = [
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'settlement_type' => $settlementType,
            'year' => $year,
            'commission_type' => $settings['commission_type'] ?? CommissionType::PERCENTAGE,
            'commission_value' => $settings['commission_value'] ?? 10,
            'tax_rate' => $settings['tax_rate'] ?? 0,
            'deductions' => $settings['deductions'] ?? 0,
            'bonuses' => $settings['bonuses'] ?? 0,
            'notes' => "Automatic {$settlementType} settlement generated for specific resource on " . Carbon::now()->format('Y-m-d H:i:s'),
        ];

        if ($month) {
            $settlementData['month'] = $month;
        }

        // Check if settlement already exists
        if (!$force) {
            $existingSettlement = $this->findExistingSettlement($settlementData);
            if ($existingSettlement) {
                return $existingSettlement; // Return existing settlement
            }
        }

        $settlement = $this->createSettlement($settlementData);
        // Don't automatically calculate - keep as pending for manual review
        
        return $settlement;
    }

    /**
     * Get a specific resource by type and ID
     */
    private function getResourceById(string $resourceType, int $resourceId)
    {
        return match($resourceType) {
            'guide' => Guide::active()->find($resourceId),
            'representative' => Representative::active()->find($resourceId),
            'hotel' => Hotel::active()->find($resourceId),
            'vehicle' => Vehicle::active()->find($resourceId),
            'dahabia' => Dahabia::active()->find($resourceId),
            'restaurant' => Restaurant::active()->find($resourceId),
            'ticket' => Ticket::active()->find($resourceId),
            'extra' => Extra::active()->find($resourceId),
            default => null
        };
    }
}
