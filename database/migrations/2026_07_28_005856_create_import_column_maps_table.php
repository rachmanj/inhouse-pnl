<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_column_maps', function (Blueprint $table) {
            $table->id();
            $table->string('source_signature', 64);
            $table->json('column_map');
            $table->unsignedInteger('times_used')->default(1);
            $table->timestamps();

            $table->unique('source_signature');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_column_maps');
    }
};
