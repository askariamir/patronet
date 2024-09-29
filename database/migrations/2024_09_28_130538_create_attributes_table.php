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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();  // Unique identifier for the attribute
            $table->string('name');  // Name of the attribute (e.g., "color", "size")
            $table->foreignId('brand_id')  // Links the attribute to a specific brand
            ->constrained()->onDelete('cascade');  // If the brand is deleted, its attributes are also deleted
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
