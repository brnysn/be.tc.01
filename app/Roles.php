<?php

namespace App;

enum Roles
{
    case Admin;
    case Customer;

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Customer => 'Customer',
        };
    }

    public static function getAllLabels(): array
    {
        return array_map(fn ($role) => $role->label(), self::cases());
    }
}
