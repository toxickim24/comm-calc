<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiffPayout extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'appointments',
        'deals_closed',
        'close_rate',
        'prior_close_rate',
        'improvement_points',
        'improvement_bonus',
        'target_bonus',
        'fast_close_count',
        'fast_close_bonus',
        'highest_close_rate_bonus',
        'total_spiff',
        'is_override',
        'override_notes',
        'settings_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'close_rate' => 'decimal:2',
            'prior_close_rate' => 'decimal:2',
            'improvement_points' => 'decimal:2',
            'improvement_bonus' => 'decimal:2',
            'target_bonus' => 'decimal:2',
            'fast_close_bonus' => 'decimal:2',
            'highest_close_rate_bonus' => 'decimal:2',
            'total_spiff' => 'decimal:2',
            'is_override' => 'boolean',
            'settings_snapshot' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
