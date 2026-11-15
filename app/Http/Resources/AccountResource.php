<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'balance' => (float) $this->balance,
            'icon' => $this->icon,
            'note' => $this->note,
            'transaction_count' => $this->when(isset($this->transaction_count), (int) $this->transaction_count),
            'month_expense' => $this->when(isset($this->month_expense), (float) $this->month_expense),
        ];
    }
}
