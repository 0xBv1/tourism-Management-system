<?php

namespace App\Enums;

enum CommissionType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';
    case NONE = 'none';

    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => ucfirst(str_replace('_', ' ', $case->value)), self::cases())
        );
    }

    public function getLabel(): string
    {
        return match($this) {
            self::PERCENTAGE => 'نسبة مئوية',
            self::FIXED => 'مبلغ ثابت',
            self::NONE => 'بدون عمولة',
        };
    }
}

