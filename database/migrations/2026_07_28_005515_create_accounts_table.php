<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('sap_code', 20)->unique();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('account_type', [
                'revenue', 'backcharge', 'cost_of_sales', 'employee_expense',
                'admin_expense', 'depreciation', 'other',
            ]);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->unsignedTinyInteger('level')->default(0);
            $table->boolean('is_postable')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('account_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
