<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the categories master table.
     *
     * Stores material categories (e.g. Raw Material, Finish Goods, Spares, Machines, Others).
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                              // Auto-increment primary key (internal category id)
            $table->string('name', 100)->unique();     // Category name — must be unique across all categories
            $table->timestamps();                      // created_at, updated_at
        });
    }

    /**
     * Drop the categories table.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
