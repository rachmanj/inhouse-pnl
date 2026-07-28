<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('metric');
            $table->decimal('observed_value', 18, 4);
            $table->decimal('expected_value', 18, 4);
            $table->decimal('z_score', 8, 4)->nullable();
            $table->text('explanation');
            $table->enum('status', ['open', 'acknowledged', 'dismissed'])->default('open');
            $table->timestamps();

            $table->index(['report_period_id', 'project_site_id', 'status'], 'aa_period_site_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_alerts');
    }
};
