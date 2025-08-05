<?php

namespace App\Http\Resources;

use App\Domains\ValueObjects\Amount;
use App\Traits\HasCurrencyFormatting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class BillResourceCollection extends ResourceCollection
{
    use HasCurrencyFormatting;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->getData(),
            'meta' => $this->getMeta(),
        ];
    }

    // Permettre l'accès direct aux données
    public function getData(): Collection
    {
        return $this->collection;
    }

    // Permettre l'accès aux métadonnées
    public function getMeta(): array
    {
        return [
            'total_count' => $this->length(),
            'total_amount' => $this->totalAmount()->value(),
            'total_amount_formatted' => $this->totalAmount()->toCurrency(),
        ];
    }

    protected function length(): int
    {
        return $this->collection->count();
    }

    protected function totalAmount(): Amount
    {
        return new Amount($this->collection->sum(function(BillResource $bill) {
            return $bill['amount']->value();
        }));
    }
}
