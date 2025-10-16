<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settlement;
use App\Models\Guide;
use App\Models\Representative;
use App\Enums\SettlementStatus;
use App\Enums\SettlementType;
use App\Enums\CommissionType;
use App\Enums\PaymentMethod;
use Carbon\Carbon;

class SettlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some guides and representatives
        $guides = Guide::active()->take(3)->get();
        $representatives = Representative::active()->take(2)->get();

        // Create sample settlements for the last 3 months
        $months = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonth(),
            Carbon::now(),
        ];

        foreach ($months as $date) {
            $month = $date->month;
            $year = $date->year;

            // Create settlements for guides
            foreach ($guides as $guide) {
                Settlement::create([
                    'settlement_type' => SettlementType::MONTHLY,
                    'resource_type' => 'guide',
                    'resource_id' => $guide->id,
                    'month' => $month,
                    'year' => $year,
                    'start_date' => $date->copy()->startOfMonth(),
                    'end_date' => $date->copy()->endOfMonth(),
                    'total_bookings' => rand(5, 15),
                    'total_hours' => rand(40, 120),
                    'total_days' => rand(10, 25),
                    'total_amount' => rand(1000, 5000),
                    'commission_type' => CommissionType::PERCENTAGE,
                    'commission_value' => 10,
                    'commission_amount' => rand(100, 500),
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'deductions' => rand(0, 100),
                    'bonuses' => rand(0, 200),
                    'net_amount' => rand(800, 4500),
                    'currency' => 'USD',
                    'status' => $this->getRandomStatus(),
                    'calculated_at' => $date->copy()->addDays(rand(1, 5)),
                    'approved_at' => $date->copy()->addDays(rand(6, 10)),
                    'paid_at' => $date->copy()->addDays(rand(11, 15)),
                    'notes' => 'Sample monthly settlement',
                ]);
            }

            // Create settlements for representatives
            foreach ($representatives as $representative) {
                Settlement::create([
                    'settlement_type' => SettlementType::MONTHLY,
                    'resource_type' => 'representative',
                    'resource_id' => $representative->id,
                    'month' => $month,
                    'year' => $year,
                    'start_date' => $date->copy()->startOfMonth(),
                    'end_date' => $date->copy()->endOfMonth(),
                    'total_bookings' => rand(3, 10),
                    'total_hours' => rand(20, 80),
                    'total_days' => rand(5, 15),
                    'total_amount' => rand(800, 3000),
                    'commission_type' => CommissionType::PERCENTAGE,
                    'commission_value' => 8,
                    'commission_amount' => rand(60, 240),
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'deductions' => rand(0, 50),
                    'bonuses' => rand(0, 100),
                    'net_amount' => rand(700, 2800),
                    'currency' => 'USD',
                    'status' => $this->getRandomStatus(),
                    'calculated_at' => $date->copy()->addDays(rand(1, 5)),
                    'approved_at' => $date->copy()->addDays(rand(6, 10)),
                    'paid_at' => $date->copy()->addDays(rand(11, 15)),
                    'notes' => 'Sample monthly settlement',
                ]);
            }
        }

        $this->command->info('Sample settlement data created successfully');
    }

    /**
     * Get a random settlement status
     */
    private function getRandomStatus(): SettlementStatus
    {
        $statuses = [
            SettlementStatus::CALCULATED,
            SettlementStatus::APPROVED,
            SettlementStatus::PAID,
        ];

        return $statuses[array_rand($statuses)];
    }
}