<?php

namespace Tests\Unit\ValueObjects;

use App\Domains\ValueObjects\Amount;
use InvalidArgumentException;

test('should represent an amount', function () {
    $amount = new Amount(1000);
    expect($amount->value())->toBe(1000);
});

test('should represent a negative amount', function () {
    $amount = new Amount(-1000);
    expect($amount->value())->toBe(-1000);
});

test('should be equal when amounts have same value', function () {
    $amount1 = new Amount(12500);
    $amount2 = new Amount(12500);

    expect($amount1->__equals($amount2))->toBeTrue()
        ->and($amount1 == $amount2)->toBeTrue();
});

test('should not be equal when amounts have different values', function () {
    $amount1 = new Amount(12500);
    $amount2 = new Amount(15000);

    expect($amount1->__equals($amount2))->toBeFalse()
        ->and($amount1 == $amount2)->toBeFalse();
});

test('should be equal when created differently but same value', function () {
    $amount1 = new Amount(12500);
    $amount2 = Amount::from('125.00');

    expect($amount1->__equals($amount2))->toBeTrue()
        ->and($amount1 == $amount2)->toBeTrue();
});

test('should be equal with zero amounts', function () {
    $amount1 = new Amount(0);
    $amount2 = new Amount(0);

    expect($amount1->__equals($amount2))->toBeTrue()
        ->and($amount1 == $amount2)->toBeTrue();
});

test('should not be equal when one is zero and other is not', function () {
    $amount1 = new Amount(0);
    $amount2 = new Amount(100);

    expect($amount1->__equals($amount2))->toBeFalse()
        ->and($amount1 == $amount2)->toBeFalse();
});

test('should create an Amount from a string', function () {
    $amount = Amount::from('100.00');
    expect($amount)->toEqual(new Amount(10000));
});

test('should create an Amount from a string with another locale formatting', function () {
    $amount = Amount::from('799,41');
    expect($amount)->toEqual(new Amount(79941));
});

test('should throw a InvalidArgumentException if the string is not a numeric value', function () {
    Amount::from('toto');
})->throws(InvalidArgumentException::class, 'Amount [toto] must be a numeric value.');

describe("should validate a numeric value…", function () {

    test('from an integer string', function () {
        expect(Amount::isValid("1000"))->toBeTrue();
    });

    test('from a decimal number value', function () {
        expect(Amount::isValid("100.00"))->toBeTrue();
    });

    test('from a string with spaces', function () {
        expect(Amount::isValid("1 799.99"))->toBeTrue();
    });

    test('from a string with currency symbol', function () {
        expect(Amount::isValid("1 799.99 €"))->toBeTrue();
    });

    test('from a string with a comma for decimal separator', function () {
        expect(Amount::isValid("99,99"))->toBeTrue();
    });

    test('from a string with other types of spaces', function () {
        expect(Amount::isValid("1 999.00 €"))->toBeTrue();
    });

    test('from a string with thousands separator', function () {
        expect(Amount::isValid("1,000.00"))->toBeTrue();
    });

    // TODO : 8,99 € // 899
    // TODO : 8,999 €  // 899900
    // TODO : 8.999.999,99 // 899999999
    // TODO : 8,999,999.99 // 899999999

    // TODO : handle negative amounts, scientific string, regional formats, etc.
});

describe("should not validate that string…", function () {
    test('which is empty', function () {
        expect(Amount::isValid(""))->toBeFalse();
    });

    test('which has no number in it', function () {
        expect(Amount::isValid("toto"))->toBeFalse();
    });

    test('which contains alphanumeric characters', function () {
        expect(Amount::isValid("abc100"))->toBeFalse();
    });

    test('which contains only digits but badly formatted', function () {
        expect(Amount::isValid("1.000,00.0000"))->toBeFalse();
    });
});


describe('extraction methods', function () {
    test('should remove currency symbols', function () {
        expect(Amount::extractCurrencySymbols("1000€"))->toBe("1000");
    });

    test('should remove white space', function () {
        expect(Amount::extractWhiteSpaces("1 000 000 000"))->toBe("1000000000");
    });

    test('should remove thousands separator', function () {
        expect(Amount::extractThousandsSeparator("1.000.000"))->toBe("1000000");
    });
});

describe('conversion methods', function () {
    test('should convert to string', function () {
        expect((string)new Amount(1000))->toBe("10,00 €");
    });

    test('should convert a negative amount to string', function () {
        expect((string)new Amount(-1000))->toBe("-10,00 €");
    });

    test('should convert to cents', function () {
        expect((new Amount(1000))->toCents())->toBe(1000);
    });

    test('should convert to decimal', function () {
        expect((new Amount(1000))->toDecimal())->toBe(10.00);
    });
});

describe("manipulation methods", function () {
    test('should add an amount to another amount', function () {
        $amountA = new Amount(1000);
        $amountB = new Amount(2000);

        expect($amountA->add($amountB))->toEqual(new Amount(3000))
            ->and($amountB->add($amountA))->toEqual(new Amount(3000));
    });
});
