<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('week_start');
            $table->date('week_end');
            $table->integer('appointments')->default(0);
            $table->integer('quotes_sent')->default(0);
            $table->integer('deals_closed')->default(0);
            $table->decimal('close_rate', 5, 2)->default(0);
            $table->decimal('avg_days_to_close', 5, 1)->default(0);
            $table->integer('fast_closes')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_scores');
    }
};
