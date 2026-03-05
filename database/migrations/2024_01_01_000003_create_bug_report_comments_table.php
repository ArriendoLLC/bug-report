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
        Schema::create('bug_report_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bug_report_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints (NO CASCADE - maintain referential integrity)
            $table->foreign('bug_report_id')
                ->references('id')
                ->on('bug_report_reports')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Indexes for performance
            $table->index(['bug_report_id', 'created_at']);
            $table->index('user_id');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_report_comments');
    }
};
