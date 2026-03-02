<?php

namespace App\Enums;

enum TravelRequestStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
