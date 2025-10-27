<?php

namespace App\Domains\ValueObjects;

use Illuminate\Support\Number;
use InvalidArgumentException;
use Livewire\Wireable;

class Amount implements Wireable
{
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $amount
     * @return string
     */
    public static function extractCurrencySymbols(string $amount): string
    {
        return preg_replace('/[€$£¥]/u', '', $amount); // currency symbol
    }

    /**
     * @param string $amount
     * @return string
     */
    public static function extractWhiteSpaces(string $amount): string
    {
        return preg_replace("/\s/u", '', $amount); // any white space
    }

    public static function extractThousandsSeparator(string $amount): string
    {
        $thousandsSeparator = [".", ",", "'", "_", " ", " ", " "];
        $decimalSeparator = [".", ","];

        if (!preg_match("/(([" . implode('', $thousandsSeparator) . "])\d{3})+([" . implode("", $decimalSeparator) . "]\d+)?$/u", $amount, $matches)) {
            return $amount;
        }

        $separator = $matches[2];
        return str_replace($separator, "", $amount);
    }

    /**
     * @param string $amount
     * @return array|string|string[]
     */
    private static function normalizeNumericString(string $amount): string|array
    {
        return str_replace(",", ".", $amount);
    }

    public function value(): int
    {
        return $this->amount;
    }


    public function toCurrency(): string
    {
        return Number::currency($this->amount / 100.0, in: 'EUR', locale: 'fr_FR');
    }

    public function toCents(): int
    {
        return $this->amount;
    }

    public function toDecimal(): float
    {
        return $this->amount / 100.0;
    }

    public function __equals(Amount $amount): bool
    {
        return $this->value() === $amount->value();
    }

    public function add(Amount $amount): Amount
    {
        return new Amount($this->value() + $amount->value());
    }

    public function subtract(Amount $amount): Amount
    {
        return new Amount($this->value() - $amount->value());
    }

    public function abs(): int
    {
        return abs($this->value());
    }

    public function opposite(): Amount
    {
        return new Amount(-$this->value());
    }

    public static function from(string $amount): Amount
    {
        $amount = str_replace(',', '.', $amount);

        if (!self::isValid($amount)) {
            throw new InvalidArgumentException("Amount [$amount] must be a numeric value.");
        }

        $amount = self::extractCurrencySymbols($amount);
        $amount = self::extractWhiteSpaces($amount);
        $amount = self::extractThousandsSeparator($amount);
        $amount = self::normalizeNumericString($amount);

        return new Amount((int)round(floatval($amount) * 100));
    }

    public static function isValid(string $amount): bool
    {
        $amount = self::extractCurrencySymbols($amount);
        $amount = self::extractWhiteSpaces($amount);
        $amount = self::extractThousandsSeparator($amount);
        $amount = self::normalizeNumericString($amount);

        return is_numeric($amount);
    }

    public function __toString(): string
    {
        return $this->toCurrency();
    }

    /**
     * Livewire: convert the value object to a primitive payload (in cents)
     */
    public function toLivewire(): int|string|array
    {
        // Return an array as Livewire's WireableSynth expects an iterable payload
        // Keep it explicit to avoid ambiguity with parent arrays of wireables
        return ['cents' => $this->toCents()];
    }

    /**
     * Livewire: restore the value object from a primitive payload
     * @param mixed $value
     */
    public static function fromLivewire($value): static
    {
        // Accept integers or numeric strings coming from JSON transport
        if (is_int($value)) {
            return new static($value);
        }

        if (is_string($value) && is_numeric($value)) {
            // Ensure we keep cents (no float involved here)
            return new static((int)$value);
        }

        // Support array payloads like ['cents' => 1234] if ever used
        if (is_array($value) && array_key_exists('cents', $value) && is_numeric($value['cents'])) {
            return new static((int)$value['cents']);
        }

        if (self::isValid($value)) {
            return self::from($value);
        }

        throw new InvalidArgumentException("Cannot hydrate Amount from provided Livewire value [$value].");
    }
}
