<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_filings', function (Blueprint $table) {
            $table->dropColumn('sarang_erp_ref_id');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tax_filings MODIFY COLUMN source ENUM('manual', 'sap') NOT NULL DEFAULT 'manual'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tax_filings MODIFY COLUMN source ENUM('manual', 'sarang_erp', 'sap') NOT NULL DEFAULT 'manual'");
        }

        Schema::table('tax_filings', function (Blueprint $table) {
            $table->unsignedBigInteger('sarang_erp_ref_id')->nullable();
        });
    }
};
