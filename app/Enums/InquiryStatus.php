<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

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

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::CANCELLED => 'red',
            self::COMPLETED => 'green',
        };
    }

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}





