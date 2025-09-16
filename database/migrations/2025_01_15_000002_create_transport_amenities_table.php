<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transport_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained('transports')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['transport_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_amenities');
    }
};
