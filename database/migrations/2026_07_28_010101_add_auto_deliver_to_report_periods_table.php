<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_periods', function (Blueprint $table) {
            $table->boolean('auto_deliver')->default(false)->after('baseline_year');
        });
    }

    public function down(): void
    {
        Schema::table('report_periods', function (Blueprint $table) {
            $table->dropColumn('auto_deliver');
        });
    }
};
