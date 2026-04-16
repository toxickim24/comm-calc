<?php

use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\Login;
use App\Livewire\CommissionCalculator;
use App\Livewire\Dashboard;
use App\Livewire\DealLog;
use App\Livewire\MonthlySpiff;
use App\Livewire\WeeklyScoreboard;
use App\Livewire\Admin\AuditLogs;
use App\Livewire\Admin\BrandingSettings;
use App\Livewire\Admin\CommissionSettings;
use App\Livewire\Admin\SpiffSettings;
use App\Livewire\Admin\UserManagement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Logout (no middleware guard needed)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Password change
    Route::get('/password/change', ChangePassword::class)->name('password.change');
    Route::get('/password/force-change', ChangePassword::class)->name('password.force-change');

    // Commission Calculator (all roles)
    Route::get('/calculator', CommissionCalculator::class)->name('commission.calculator');

    // Deals (all roles)
    Route::get('/deals', DealLog::class)->name('deals.index');

    // Scoreboard (all roles)
    Route::get('/scoreboard', WeeklyScoreboard::class)->name('scoreboard.index');

    // Monthly SPIFF (admin + manager)
    Route::get('/spiff', MonthlySpiff::class)
        ->middleware('role:admin,manager')
        ->name('spiff.index');

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/users', UserManagement::class)->name('admin.users');
        Route::get('/commission-settings', CommissionSettings::class)->name('admin.commission-settings');
        Route::get('/spiff-settings', SpiffSettings::class)->name('admin.spiff-settings');
        Route::get('/branding', BrandingSettings::class)->name('admin.branding');
        Route::get('/audit-logs', AuditLogs::class)->name('admin.audit-logs');
    });
});
