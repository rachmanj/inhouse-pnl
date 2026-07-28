<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variance_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pnl_line_id')->constrained()->cascadeOnDelete();
            $table->enum('comparison_type', ['yoy', 'mom', 'budget']);
            $table->decimal('delta_absolute', 18, 2);
            $table->decimal('delta_percent', 8, 2);
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('is_acknowledged')->default(false);
            $table->timestamps();

            $table->index(['report_period_id', 'project_site_id', 'severity'], 'vf_period_site_severity_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variance_flags');
    }
};
