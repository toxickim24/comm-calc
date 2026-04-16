<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spiff_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('month');
            $table->integer('appointments')->default(0);
            $table->integer('deals_closed')->default(0);
            $table->decimal('close_rate', 5, 2)->default(0);
            $table->decimal('prior_close_rate', 5, 2)->default(0);
            $table->decimal('improvement_points', 5, 2)->default(0);
            $table->decimal('improvement_bonus', 10, 2)->default(0);
            $table->decimal('target_bonus', 10, 2)->default(0);
            $table->integer('fast_close_count')->default(0);
            $table->decimal('fast_close_bonus', 10, 2)->default(0);
            $table->decimal('highest_close_rate_bonus', 10, 2)->default(0);
            $table->decimal('total_spiff', 10, 2)->default(0);
            $table->boolean('is_override')->default(false);
            $table->text('override_notes')->nullable();
            $table->json('settings_snapshot')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spiff_payouts');
    }
};
