<?php

namespace App\Enums;

enum DistributionMethod: string
{
    case EQUAL = 'equal';
    case PRORATA = 'prorata';

    public function label(): string
    {
        return match ($this) {
            self::EQUAL => '50/50',
            self::PRORATA => 'Prorata',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EQUAL => 'Les membres du foyer paient chacun la moitié du montant total.',
            self::PRORATA => 'Les membres du foyer paient proportionnellement à leur consommation.',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            function (DistributionMethod $method) {
                return [$method->value => $method->label()];
            })->toArray();
    }

    public static function labels(): array
    {
        return array_map(
            function(DistributionMethod $method) { return $method->label(); },
            self::cases()
        );
    }
}
