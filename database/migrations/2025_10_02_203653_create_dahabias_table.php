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
        Schema::create('dahabias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->decimal('vessel_length', 5, 2);
            $table->integer('capacity');
            $table->decimal('price_per_person', 10, 2);
            $table->decimal('price_per_charter', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('departure_location');
            $table->string('arrival_location');
            $table->text('route_description')->nullable();
            $table->text('sailing_schedule')->nullable();
            $table->string('meal_plan')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('status')->default('available');
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->integer('crew_count')->nullable();
            $table->integer('duration_nights');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
            $table->index(['active', 'enabled']);
            $table->index('capacity');
            $table->index('duration_nights');
            $table->index('vessel_length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dahabias');
    }
};
