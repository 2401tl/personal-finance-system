<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $category = $this->relationLoaded('category') ? $this->category : null;
        $parent = $category?->parent_id ? $category->parent : $category;
        $sub = $category?->parent_id ? $category : null;

        return [
            'id' => $this->id,
            'date' => $this->date?->format('Y-m-d'),
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'note' => $this->note ?? '-',
            'parent_id' => $parent?->id,
            'parent_name' => $parent?->name,
            'sub_id' => $sub?->id,
            'sub_name' => $sub?->name,
            'account' => $this->relationLoaded('account') ? $this->account?->name : null,
            'account_id' => $this->account_id,
            'icon' => $sub?->icon ?? $parent?->icon,
            'color' => $parent?->color ?? $sub?->color,
            'transfer_to_account' => $this->relationLoaded('transferToAccount') ? $this->transferToAccount?->name : null,
        ];
    }
}
