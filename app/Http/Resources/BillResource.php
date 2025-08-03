<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'amount_formatted' => $this->amount->__tostring(),
            'distribution_method' => $this->distribution_method->value,
            'distribution_method_label' => $this->distribution_method->label(),
            'member' => [
                'id' => $this->member->id,
                'full_name' => $this->member->full_name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
