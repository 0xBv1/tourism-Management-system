<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

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

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::IN_PROGRESS => 'purple',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
            self::REFUNDED => 'gray',
        };
    }

    public function getLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
