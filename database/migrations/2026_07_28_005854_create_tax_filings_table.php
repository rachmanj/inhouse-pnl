<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tax_type', ['ppn', 'pph21', 'pph23', 'pph25', 'pph4a2']);
            $table->string('filing_number')->nullable();
            $table->date('due_date');
            $table->timestamp('filed_at')->nullable();
            $table->enum('status', ['pending', 'filed', 'late'])->default('pending');
            $table->decimal('amount_reported', 18, 2)->default(0);
            $table->enum('source', ['manual', 'sarang_erp', 'sap'])->default('manual');
            $table->unsignedBigInteger('sarang_erp_ref_id')->nullable();
            $table->timestamps();

            $table->index(['report_period_id', 'tax_type']);
            $table->index(['project_site_id', 'tax_type']);
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_filings');
    }
};
