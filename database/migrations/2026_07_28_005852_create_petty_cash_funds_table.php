<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('replenishment_amount', 18, 2)->default(0);
            $table->decimal('closing_balance', 18, 2)->default(0);
            $table->enum('status', ['open', 'reconciled'])->default('open');
            $table->timestamps();

            $table->unique(['project_site_id', 'report_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_funds');
    }
};
