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
        Schema::create('bug_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bug_report_id');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 100);
            $table->unsignedInteger('file_size'); // in bytes
            $table->timestamps();

            // Foreign key constraint (NO CASCADE - maintain referential integrity)
            $table->foreign('bug_report_id')
                ->references('id')
                ->on('bug_report_reports')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Index for performance
            $table->index('bug_report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_report_attachments');
    }
};
