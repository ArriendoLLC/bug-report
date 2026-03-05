<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bug_report_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->text('description');
            $table->string('status', 50)->default('new');
            $table->string('url', 2048)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint (NO CASCADE - maintain referential integrity)
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Indexes for performance
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_report_reports');
    }
};
