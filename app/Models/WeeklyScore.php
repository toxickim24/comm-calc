<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyScore extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'appointments',
        'quotes_sent',
        'deals_closed',
        'close_rate',
        'avg_days_to_close',
        'fast_closes',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'close_rate' => 'decimal:2',
            'avg_days_to_close' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
