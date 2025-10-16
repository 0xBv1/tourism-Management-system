<?php

namespace App\Enums;

enum SettlementStatus: string
{
    case PENDING = 'pending';
    case CALCULATED = 'calculated';
    case APPROVED = 'approved';
    case PAID = 'paid';
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

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CALCULATED => 'info',
            self::APPROVED => 'primary',
            self::PAID => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'في الانتظار',
            self::CALCULATED => 'محسوب',
            self::APPROVED => 'معتمد',
            self::PAID => 'مدفوع',
            self::REJECTED => 'مرفوض',
        };
    }
}
