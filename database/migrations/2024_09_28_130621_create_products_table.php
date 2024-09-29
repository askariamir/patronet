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
        Schema::create('products', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');  // Foreign key to the brand
            $table->string('product_code');  // Field for the product code (e.g., B-S-H or B-SQ-H-V)
            $table->json('combination');  // JSON field to store the attribute combination
            $table->timestamps();  // Timestamps for tracking
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
