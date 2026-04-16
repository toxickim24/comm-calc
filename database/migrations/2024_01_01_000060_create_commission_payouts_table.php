<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('month');
            $table->decimal('sold_contract_value', 12, 2);
            $table->decimal('gm_percent', 5, 2);
            $table->string('tier', 50);
            $table->decimal('base_commission', 10, 2)->default(0);
            $table->decimal('surplus_bonus', 10, 2)->default(0);
            $table->decimal('fast_close_bonus', 10, 2)->default(0);
            $table->decimal('total_payout', 10, 2)->default(0);
            $table->json('settings_snapshot')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_payouts');
    }
};
