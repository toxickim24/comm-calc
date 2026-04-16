<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CommissionSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'label',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->updated_at = now();
        });
    }

    public static function getValue(string $key, float $default = 0): float
    {
        return (float) (static::where('key', $key)->value('value') ?? $default);
    }

    public static function allAsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
