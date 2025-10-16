<?php

namespace App\Enums;

enum SettlementType: string
{
    case MONTHLY = 'monthly';
    case WEEKLY = 'weekly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';
    case CUSTOM = 'custom';

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
            self::MONTHLY => 'شهري',
            self::WEEKLY => 'أسبوعي',
            self::QUARTERLY => 'ربعي',
            self::YEARLY => 'سنوي',
            self::CUSTOM => 'مخصص',
        };
    }
}

