<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the inward_outward transactions table.
     *
     * Records every inward (positive) and outward (negative) quantity movement
     * against a material. The current balance of a material is:
     *   opening_balance + SUM(quantity) over all matching rows.
     */
    public function up(): void
    {
        Schema::create('inward_outward', function (Blueprint $table) {
            $table->id();                                       // Auto-increment primary key (internal inward id)

            // FK to materials — CASCADE so transactions are removed if a material is force-deleted
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')
                  ->references('id')
                  ->on('materials')
                  ->cascadeOnDelete();

            $table->date('date');                               // Transaction date (calendar input)
            $table->decimal('quantity', 15, 2);                 // Positive = inward, negative = outward (max 2 dp)
            $table->timestamps();                               // created_at, updated_at

            // Index for fast balance calculations (SUM queries filter by material_id)
            $table->index('material_id', 'idx_io_material_id');

            // Index to allow date-range filtering efficiently
            $table->index('date', 'idx_io_date');
        });
    }

    /**
     * Drop the inward_outward table.
     * Foreign key constraints are dropped automatically when the table is dropped.
     */
    public function down(): void
    {
        Schema::dropIfExists('inward_outward');
    }
};
