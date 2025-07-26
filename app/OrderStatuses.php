<?php

namespace App;

enum OrderStatuses
{
    case Pending;
    case Approved;
    case Shipped;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Shipped => 'Shipped',
        };
    }

    public static function getAllLabels(): array
    {
        return array_map(fn ($status) => $status->label(), self::cases());
    }
}
