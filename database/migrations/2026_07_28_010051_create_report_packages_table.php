<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'in_review', 'approved', 'delivered'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_packages');
    }
};
