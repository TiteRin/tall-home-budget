<?php

namespace App\Rules;

use App\Domains\ValueObjects\Amount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidAmount implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Amount::isValid($value)) {
            $fail("The :attribute must be a valid amount.");
        }
    }
}
