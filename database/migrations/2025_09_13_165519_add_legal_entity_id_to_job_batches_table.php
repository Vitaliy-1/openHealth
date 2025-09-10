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
        Schema::table('job_batches', function (Blueprint $table) {
            // Add legal_entity_id field to track which legal entity the batch belongs to
            $table->unsignedBigInteger('legal_entity_id')->nullable()->after('options');

            // Add foreign key constraint to legal_entities table
            $table->foreign('legal_entity_id')
                  ->references('id')
                  ->on('legal_entities')
                  ->onDelete('set null'); // Set to null if legal entity is deleted

            // Add index for better query performance
            $table->index(['legal_entity_id', 'created_at'], 'idx_job_batches_legal_entity_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_batches', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['legal_entity_id']);

            // Drop index
            $table->dropIndex('idx_job_batches_legal_entity_created');

            // Drop the column
            $table->dropColumn('legal_entity_id');
        });
    }
};
