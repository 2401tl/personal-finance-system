<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'type', 'parent_id', 'name', 'icon', 'color', 'budget', 'sort_order'])]
class Category extends Model
{
    public const TYPE_EXPENSE = 'expense';

    public const TYPE_INCOME = 'income';

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
