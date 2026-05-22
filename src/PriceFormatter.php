<?php

declare(strict_types=1);

namespace App;

final class PriceFormatter
{
    public static function format(int $priceInMinorUnits, string $currency): string
    {
        $amount = $priceInMinorUnits / 100;

        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }
}
