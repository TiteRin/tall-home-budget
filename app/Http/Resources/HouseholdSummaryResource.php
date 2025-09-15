<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseholdSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_amount' => $this->total_amount,
            'total_amount_formatted' => $this->total_amount_formatted,
            'default_distribution_method' => $this->default_distribution_method->value,
            'default_distribution_method_label' => $this->default_distribution_method->label(),
        ];
    }
} 