<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coa_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pnl_line_id')->constrained()->cascadeOnDelete();
            $table->date('effective_from');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['account_id', 'effective_from'], 'coa_mappings_account_effective_unique');
            $table->index('pnl_line_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coa_mappings');
    }
};
