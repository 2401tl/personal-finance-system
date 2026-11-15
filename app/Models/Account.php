<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'type', 'balance', 'icon', 'note'])]
class Account extends Model
{
    public const TYPE_BANK = 'bank';

    public const TYPE_ALIPAY = 'alipay';

    public const TYPE_WECHAT = 'wechat';

    public const TYPE_CASH = 'cash';

    public static function typeNames(): array
    {
        return [
            self::TYPE_BANK => '银行卡',
            self::TYPE_ALIPAY => '支付宝',
            self::TYPE_WECHAT => '微信支付',
            self::TYPE_CASH => '现金',
        ];
    }

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTypeNameAttribute(): string
    {
        return self::typeNames()[$this->type] ?? $this->type;
    }
}
