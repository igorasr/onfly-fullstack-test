<?php

namespace App\Enums;

enum TravelRequestStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Cancelled = 'cancelled';


    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Requested => in_array($to, [self::Approved, self::Cancelled], true),
            self::Approved   => $to === self::Cancelled,
            self::Cancelled  => $to === self::Requested
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
