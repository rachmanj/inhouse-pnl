<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_artifacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_package_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['excel', 'pdf']);
            $table->string('file_path');
            $table->string('file_hash', 64);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_artifacts');
    }
};
