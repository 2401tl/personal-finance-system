<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'settings'])]
class UserSetting extends Model
{
    public static function defaults(): array
    {
        return [
            'currency' => 'CNY',
            'currency_symbol' => '¥',
            'date_format' => 'Y-m-d',
            'week_start' => 'monday',
            'page_size' => 10,
            'budget_alert' => true,
            'budget_threshold' => 80,
            'daily_reminder' => false,
            'reminder_time' => '20:00',
            'theme' => 'light',
            'compact_mode' => false,
            'chart_animation' => true,
            'auto_backup' => true,
            'backup_frequency' => 'weekly',
            'app_lock' => false,
            'auto_lock' => 5,
        ];
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default ?? data_get(self::defaults(), $key));
    }

    public function set(string $key, mixed $value): void
    {
        $settings = $this->settings ?? self::defaults();
        data_set($settings, $key, $value);
        $this->settings = $settings;
    }
}
