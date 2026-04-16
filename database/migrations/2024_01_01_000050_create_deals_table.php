<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('month');
            $table->string('client_name');
            $table->date('appointment_date');
            $table->date('contract_signed_date')->nullable();
            $table->date('deposit_date')->nullable();
            $table->decimal('original_contract_price', 12, 2)->default(0);
            $table->decimal('sold_contract_value', 12, 2)->default(0);
            $table->decimal('estimated_gm_percent', 5, 2)->default(0);
            $table->enum('deal_status', ['lead', 'appointment_set', 'quote_sent', 'closed_won', 'closed_lost'])->default('lead');
            $table->integer('days_to_close')->nullable();
            $table->boolean('is_fast_close')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'month']);
            $table->index('deal_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
