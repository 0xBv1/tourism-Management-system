<?php

namespace App\Enums;

enum CouponType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => ucfirst($case->value), self::cases())
        );
    }
}
