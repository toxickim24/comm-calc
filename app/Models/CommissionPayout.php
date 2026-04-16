<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayout extends Model
{
    protected $fillable = [
        'deal_id',
        'user_id',
        'month',
        'sold_contract_value',
        'gm_percent',
        'tier',
        'base_commission',
        'surplus_bonus',
        'fast_close_bonus',
        'total_payout',
        'settings_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'sold_contract_value' => 'decimal:2',
            'gm_percent' => 'decimal:2',
            'base_commission' => 'decimal:2',
            'surplus_bonus' => 'decimal:2',
            'fast_close_bonus' => 'decimal:2',
            'total_payout' => 'decimal:2',
            'settings_snapshot' => 'array',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
