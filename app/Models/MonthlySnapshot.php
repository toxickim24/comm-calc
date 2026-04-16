<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySnapshot extends Model
{
    protected $fillable = [
        'month',
        'is_locked',
        'locked_at',
        'locked_by',
        'unlocked_at',
        'unlocked_by',
        'commission_settings_snapshot',
        'spiff_settings_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'unlocked_at' => 'datetime',
            'commission_settings_snapshot' => 'array',
            'spiff_settings_snapshot' => 'array',
        ];
    }

    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function unlockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    public static function isMonthLocked(string $month): bool
    {
        return static::where('month', $month)->where('is_locked', true)->exists();
    }
}
