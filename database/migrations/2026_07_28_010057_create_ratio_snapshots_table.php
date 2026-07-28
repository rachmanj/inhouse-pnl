<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratio_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->constrained()->cascadeOnDelete();
            $table->string('ratio_code', 40);
            $table->decimal('value', 14, 4);
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->unique(['report_period_id', 'project_site_id', 'ratio_code'], 'ratio_snapshots_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratio_snapshots');
    }
};
