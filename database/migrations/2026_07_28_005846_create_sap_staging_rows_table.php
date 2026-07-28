<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sap_staging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->string('raw_account_code', 30)->nullable();
            $table->string('raw_account_name')->nullable();
            $table->decimal('raw_debit', 18, 2)->nullable();
            $table->decimal('raw_credit', 18, 2)->nullable();
            $table->decimal('raw_balance', 18, 2)->nullable();
            $table->json('raw_payload');
            $table->foreignId('mapped_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('mapping_status', ['unmapped', 'mapped', 'ambiguous', 'error'])->default('unmapped');
            $table->string('error_message')->nullable();
            $table->timestamps();

            $table->index(['import_batch_id', 'mapping_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sap_staging');
    }
};
