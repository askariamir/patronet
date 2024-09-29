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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('attribute_id')  // Foreign key to the attributes table
            ->constrained()->onDelete('cascade');
            $table->string('value');  // The shorthand value (e.g., "B" for Blue)
            $table->string('description');  // Human-readable description (e.g., "Blue")
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
