<?php

namespace App\Console\Commands;

use App\Services\SettlementService;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Dahabia;
use App\Models\Restaurant;
use App\Models\Ticket;
use App\Models\Extra;
use App\Enums\SettlementType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSettlementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settlements:generate 
                            {--type=monthly : Settlement type (monthly, weekly, quarterly, yearly)}
                            {--month= : Month for monthly settlements (1-12)}
                            {--year= : Year for settlements (defaults to current year)}
                            {--resource-type= : Specific resource type to process (guide, representative, hotel, vehicle, dahabia, restaurant, ticket, extra)}
                            {--resource-id= : Specific resource ID to process}
                            {--force : Force regeneration even if settlement exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate settlements automatically for all resources or specific resources';

    protected SettlementService $settlementService;

    public function __construct(SettlementService $settlementService)
    {
        parent::__construct();
        $this->settlementService = $settlementService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $month = $this->option('month');
        $year = $this->option('year') ?: Carbon::now()->year;
        $resourceType = $this->option('resource-type');
        $resourceId = $this->option('resource-id');
        $force = $this->option('force');

        $this->info("Generating {$type} settlements for year {$year}...");

        try {
            if ($resourceType && $resourceId) {
                // Generate for specific resource
                $this->generateForSpecificResource($resourceType, $resourceId, $type, $month, $year, $force);
            } elseif ($resourceType) {
                // Generate for all resources of specific type
                $this->generateForResourceType($resourceType, $type, $month, $year, $force);
            } else {
                // Generate for all resources
                $this->generateForAllResources($type, $month, $year, $force);
            }

            $this->info('Settlement generation completed successfully!');

        } catch (\Exception $e) {
            $this->error('Error generating settlements: ' . $e->getMessage());
            Log::error('Settlement generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Generate settlements for all resources
     */
    private function generateForAllResources(string $type, ?int $month, int $year, bool $force): void
    {
        $resourceTypes = ['guide', 'representative', 'hotel', 'vehicle', 'dahabia', 'restaurant', 'ticket', 'extra'];
        
        foreach ($resourceTypes as $resourceType) {
            $this->generateForResourceType($resourceType, $type, $month, $year, $force);
        }
    }

    /**
     * Generate settlements for specific resource type
     */
    private function generateForResourceType(string $resourceType, string $type, ?int $month, int $year, bool $force): void
    {
        $this->info("Processing {$resourceType}s...");
        
        $resources = $this->getResourcesByType($resourceType);
        
        if ($resources->isEmpty()) {
            $this->warn("No active {$resourceType}s found.");
            return;
        }

        $progressBar = $this->output->createProgressBar($resources->count());
        $progressBar->start();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($resources as $resource) {
            try {
                $result = $this->generateSettlementForResource($resource, $resourceType, $type, $month, $year, $force);
                
                if ($result['created']) {
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing {$resourceType} ID {$resource->id}: " . $e->getMessage());
                Log::error("Settlement generation failed for {$resourceType} ID {$resource->id}", [
                    'error' => $e->getMessage()
                ]);
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        
        $this->info("{$resourceType}s: {$createdCount} settlements created, {$skippedCount} skipped");
    }

    /**
     * Generate settlement for specific resource
     */
    private function generateForSpecificResource(string $resourceType, int $resourceId, string $type, ?int $month, int $year, bool $force): void
    {
        $resource = $this->getResourceById($resourceType, $resourceId);
        
        if (!$resource) {
            $this->error("{$resourceType} with ID {$resourceId} not found or inactive.");
            return;
        }

        $this->info("Processing {$resourceType} ID {$resourceId} ({$resource->name})...");
        
        $result = $this->generateSettlementForResource($resource, $resourceType, $type, $month, $year, $force);
        
        if ($result['created']) {
            $this->info("Settlement created successfully!");
        } else {
            $this->warn("Settlement already exists. Use --force to regenerate.");
        }
    }

    /**
     * Generate settlement for a single resource
     */
    private function generateSettlementForResource($resource, string $resourceType, string $type, ?int $month, int $year, bool $force): array
    {
        $settlementData = [
            'resource_type' => $resourceType,
            'resource_id' => $resource->id,
            'settlement_type' => $type,
            'year' => $year,
            'commission_type' => 'percentage',
            'commission_value' => 10, // Default 10% commission
            'tax_rate' => 0,
            'notes' => "Automatic {$type} settlement generated on " . Carbon::now()->format('Y-m-d H:i:s'),
        ];

        // Add month for monthly settlements
        if ($type === 'monthly') {
            $settlementData['month'] = $month ?: Carbon::now()->month;
        }

        // Add custom dates for custom settlements
        if ($type === 'custom') {
            $dates = $this->calculateCustomDates($year, $month);
            $settlementData['start_date'] = $dates['start_date'];
            $settlementData['end_date'] = $dates['end_date'];
        }

        // Check if settlement already exists
        if (!$force) {
            $existingSettlement = $this->settlementService->findExistingSettlement($settlementData);
            if ($existingSettlement) {
                return ['created' => false, 'settlement' => $existingSettlement];
            }
        }

        // Create settlement
        $settlement = $this->settlementService->createSettlement($settlementData);
        $this->settlementService->calculateSettlement($settlement);

        return ['created' => true, 'settlement' => $settlement];
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
     * Get specific resource by ID
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

    /**
     * Calculate custom dates for custom settlements
     */
    private function calculateCustomDates(int $year, ?int $month): array
    {
        if ($month) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = Carbon::create($year, 12, 31);
        }

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
    }
}

