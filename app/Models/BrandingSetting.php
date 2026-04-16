<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'logo_path',
        'company_name',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->updated_at = now();
        });
    }

    public static function current(): static
    {
        return static::firstOrCreate([], [
            'company_name' => 'Bayside Pavers',
        ]);
    }

    public function logoUrl(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return asset('storage/' . $this->logo_path);
    }
}
