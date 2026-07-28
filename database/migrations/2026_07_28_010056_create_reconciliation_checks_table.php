<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliation_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('checkable_type')->nullable();
            $table->unsignedBigInteger('checkable_id')->nullable();
            $table->decimal('sap_control_total', 18, 2)->nullable();
            $table->decimal('system_total', 18, 2);
            $table->decimal('discrepancy', 18, 2);
            $table->boolean('is_reconciled')->default(false);
            $table->json('discrepancy_detail')->nullable();
            $table->timestamps();

            $table->index(['checkable_type', 'checkable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_checks');
    }
};
