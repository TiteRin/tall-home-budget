<?php

namespace App\Traits;

use NumberFormatter;

trait HasCurrencyFormatting
{

    private const EURO_CURRENCY = 'EUR';
    private const FRENCH_LOCALE = 'fr_FR';
    private const CENTIMES_TO_EUROS_RATIO = 100.0;


    protected function formatCurrency(
        int $montantEnCentimes, 
        string $locale = self::FRENCH_LOCALE
    ): string
    {
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $numberFormatter->formatCurrency($montantEnCentimes / self::CENTIMES_TO_EUROS_RATIO, self::EURO_CURRENCY);
    }
} 