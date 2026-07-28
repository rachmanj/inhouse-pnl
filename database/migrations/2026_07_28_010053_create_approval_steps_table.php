<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_site_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('step_order');
            $table->string('approver_role');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('acted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acted_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index(['report_package_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
