<?php

namespace App\Enums;

enum ActivityType: string
{
    case MATERIAL_TRANSFER = 'Material Transfer';
    case SALES = 'Sales';
    case INTERNAL_MOVEMENT = 'Internal Movement';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
