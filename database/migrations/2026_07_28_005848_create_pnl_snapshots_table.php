<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['report_period_id', 'project_site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_snapshots');
    }
};
