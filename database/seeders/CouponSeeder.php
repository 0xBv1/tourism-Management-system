<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Tour;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $tours = Tour::all();

        $coupons = [
            [
                'title' => 'Welcome to 2024',
                'code' => 'WELCOME2024',
                'active' => true,
                'value' => 20.0,
                'discount_type' => 'percentage',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(6),
                'limit_per_usage' => 100,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'Summer Special',
                'code' => 'SUMMER50',
                'active' => true,
                'value' => 50.0,
                'discount_type' => 'fixed',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'limit_per_usage' => 50,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'Adventure Discount',
                'code' => 'ADVENTURE25',
                'active' => true,
                'value' => 25.0,
                'discount_type' => 'percentage',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(4),
                'limit_per_usage' => 75,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'First Time Customer',
                'code' => 'FIRSTTIME',
                'active' => true,
                'value' => 15.0,
                'discount_type' => 'percentage',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYear(),
                'limit_per_usage' => 200,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'Weekend Getaway',
                'code' => 'WEEKEND30',
                'active' => true,
                'value' => 30.0,
                'discount_type' => 'percentage',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'limit_per_usage' => 60,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'VIP Customer',
                'code' => 'VIP100',
                'active' => true,
                'value' => 100.0,
                'discount_type' => 'fixed',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(12),
                'limit_per_usage' => 25,
                'limit_per_customer' => 1,
            ],
            [
                'title' => 'Flash Sale',
                'code' => 'FLASH40',
                'active' => true,
                'value' => 40.0,
                'discount_type' => 'percentage',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(7),
                'limit_per_usage' => 30,
                'limit_per_customer' => 1,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        // Create some category-specific coupons
        if ($categories->count() > 0) {
            $categoryCoupons = [
                [
                    'title' => 'Adventure Category Discount',
                    'code' => 'ADVENTURE_CAT',
                    'active' => true,
                    'value' => 20.0,
                    'discount_type' => 'percentage',
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addMonths(3),
                    'limit_per_usage' => 50,
                    'limit_per_customer' => 1,
                ],
                [
                    'title' => 'Cultural Category Discount',
                    'code' => 'CULTURAL_CAT',
                    'active' => true,
                    'value' => 15.0,
                    'discount_type' => 'percentage',
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addMonths(3),
                    'limit_per_usage' => 50,
                    'limit_per_customer' => 1,
                ],
            ];

            foreach ($categoryCoupons as $couponData) {
                Coupon::firstOrCreate(
                    ['code' => $couponData['code']],
                    $couponData
                );
            }
        }

        $this->command->info('Coupons seeded successfully!');
    }
}

