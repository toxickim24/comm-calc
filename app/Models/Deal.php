<?php

namespace App\Models;

use App\Enums\DealStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'month',
        'client_name',
        'appointment_date',
        'contract_signed_date',
        'deposit_date',
        'original_contract_price',
        'sold_contract_value',
        'estimated_gm_percent',
        'deal_status',
        'days_to_close',
        'is_fast_close',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'appointment_date' => 'date',
            'contract_signed_date' => 'date',
            'deposit_date' => 'date',
            'original_contract_price' => 'decimal:2',
            'sold_contract_value' => 'decimal:2',
            'estimated_gm_percent' => 'decimal:2',
            'deal_status' => DealStatus::class,
            'is_fast_close' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commissionPayout(): HasOne
    {
        return $this->hasOne(CommissionPayout::class);
    }

    public function isClosedWon(): bool
    {
        return $this->deal_status === DealStatus::ClosedWon;
    }
}
