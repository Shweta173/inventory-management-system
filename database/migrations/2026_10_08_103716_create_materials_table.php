<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the materials table.
     *
     * Each material belongs to a category and carries an opening balance.
     * Soft-deleted via deleted_at (records are never hard-deleted from this table).
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();                                       // Auto-increment primary key (internal material id)

            // FK to categories — RESTRICT prevents deleting a category that still has materials
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->restrictOnDelete();

            $table->string('name', 255);                        // Material name — alphanumeric
            $table->decimal('opening_balance', 15, 2)
                  ->default(0.00);                              // Opening balance — max 2 decimal places

            $table->softDeletes();                              // deleted_at — soft delete flag (never hard-deleted)
            $table->timestamps();                               // created_at, updated_at

            // Composite unique: same material name cannot appear twice in the same category
            $table->unique(['category_id', 'name'], 'uq_materials_category_name');

            // Index for fast lookups by category
            $table->index('category_id', 'idx_materials_category_id');
        });
    }

    /**
     * Drop the materials table.
     * Foreign key constraints are dropped automatically when the table is dropped.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
