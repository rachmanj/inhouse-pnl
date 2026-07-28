<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->decimal('balance', 18, 2)->default(0);
            $table->enum('source', ['sap', 'upload', 'email', 'sister_app'])->default('upload');
            $table->foreignId('import_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(
                ['report_period_id', 'project_site_id', 'account_id', 'source'],
                'account_balances_period_site_account_source_unique'
            );
            $table->index(['report_period_id', 'project_site_id', 'account_id'], 'ab_period_site_account_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
