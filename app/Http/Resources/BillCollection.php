<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Traits\HasCurrencyFormatting;

class BillCollection extends ResourceCollection
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
    public function getData()
    {
        return $this->collection;
    }

    // Permettre l'accès aux métadonnées
    public function getMeta()
    {
        return [
            'total_count' => $this->collection->count(),
            'total_amount' => $this->collection->sum('amount') ?? 0,
            'total_amount_formatted' => $this->formatCurrency($this->collection->sum('amount') ?? 0),
        ];
    }
} 