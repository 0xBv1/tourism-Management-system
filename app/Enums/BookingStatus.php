<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case HOLD = 'hold';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';
    case REJECTED = 'rejected';

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
}
