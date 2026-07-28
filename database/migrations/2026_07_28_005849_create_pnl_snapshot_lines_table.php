<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_snapshot_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnl_snapshot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pnl_line_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['pnl_snapshot_id', 'pnl_line_id', 'year', 'month'],
                'pnl_snapshot_lines_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_snapshot_lines');
    }
};
