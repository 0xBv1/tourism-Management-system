<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case NOT_PAID = 'not_paid';
    case PENDING = 'pending';

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
            self::PAID => 'green',
            self::NOT_PAID => 'red',
            self::PENDING => 'yellow',
        };
    }

    public function getLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
