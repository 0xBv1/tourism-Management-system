<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SettlementService;
use App\Enums\SettlementType;
use App\Enums\CommissionType;
use Carbon\Carbon;

class GenerateMonthlySettlements extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'settlements:generate-monthly 
                            {--month= : Month (1-12)}
                            {--year= : Year}
                            {--commission-type=percentage : Commission type (percentage, fixed, none)}
                            {--commission-value=10 : Commission value}
                            {--tax-rate=0 : Tax rate}
                            {--force : Force creation even if settlements exist}';

    /**
     * The console command description.
     */
    protected $description = 'Generate monthly settlements for guides and representatives';

    protected $settlementService;

    /**
     * Create a new command instance.
     */
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
        $this->info('Starting monthly settlements generation...');

        // Get parameters
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?: Carbon::now()->subMonth()->year;
        $commissionType = $this->option('commission-type');
        $commissionValue = (float) $this->option('commission-value');
        $taxRate = (float) $this->option('tax-rate');
        $force = $this->option('force');

        // Validate parameters
        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12');
            return 1;
        }

        if ($year < 2020 || $year > 2030) {
            $this->error('Year must be between 2020 and 2030');
            return 1;
        }

        if (!in_array($commissionType, ['percentage', 'fixed', 'none'])) {
            $this->error('Commission type must be: percentage, fixed, or none');
            return 1;
        }

        if ($commissionType !== 'none' && $commissionValue < 0) {
            $this->error('Commission value must be greater than or equal to zero');
            return 1;
        }

        if ($taxRate < 0 || $taxRate > 100) {
            $this->error('Tax rate must be between 0 and 100');
            return 1;
        }

        $this->info("Generating settlements for month: {$month} / year: {$year}");
        $this->info("Commission type: {$commissionType} / value: {$commissionValue}");
        $this->info("Tax rate: {$taxRate}%");

        if (!$force) {
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Operation cancelled');
                return 0;
            }
        }

        try {
            $settings = [
                'commission_type' => $commissionType,
                'commission_value' => $commissionValue,
                'tax_rate' => $taxRate,
            ];

            $settlements = $this->settlementService->generateMonthlySettlements($month, $year, $settings);

            $this->info("Successfully created " . count($settlements) . " settlements");

            // Display summary
            $this->table(
                ['Settlement Number', 'Resource', 'Resource Type', 'Total Amount', 'Net Amount', 'Status'],
                collect($settlements)->map(function ($settlement) {
                    return [
                        $settlement->settlement_number,
                        $settlement->resource_name,
                        $settlement->resource_type === 'guide' ? 'Guide' : 'Representative',
                        $settlement->formatted_amount,
                        $settlement->formatted_net_amount,
                        $settlement->status_label,
                    ];
                })
            );

            $this->info('Monthly settlements generation completed successfully');

        } catch (\Exception $e) {
            $this->error('Error generating settlements: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}