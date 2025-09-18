<?php

namespace App\Enums;

enum ResourceStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';
    case OUT_OF_SERVICE = 'out_of_service';

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
            self::AVAILABLE => 'green',
            self::OCCUPIED => 'red',
            self::MAINTENANCE => 'yellow',
            self::OUT_OF_SERVICE => 'gray',
        };
    }

    public function getLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}




