<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'icon' => $this->icon,
            'color' => $this->color,
            'budget' => $this->budget ? (float) $this->budget : null,
            'sort_order' => $this->sort_order,
            'count' => $this->whenCounted('transactions'),
            'subcategories' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
