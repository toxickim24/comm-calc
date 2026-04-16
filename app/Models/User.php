<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'force_password_change',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'force_password_change' => 'boolean',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isSalesRep(): bool
    {
        return $this->role === UserRole::SalesRep;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function commissionPayouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function spiffPayouts(): HasMany
    {
        return $this->hasMany(SpiffPayout::class);
    }

    public function weeklyScores(): HasMany
    {
        return $this->hasMany(WeeklyScore::class);
    }

    public function passwordChangeLogs(): HasMany
    {
        return $this->hasMany(PasswordChangeLog::class);
    }
}
