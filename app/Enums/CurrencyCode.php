<?php

namespace App\Enums;

enum CurrencyCode: string
{
    case UAH = 'UAH';
    case USD = 'USD';
    case EUR = 'EUR';

    public static function values(): array
    {
        return array_map(fn (self $code) => $code->value, self::cases());
    }
}
