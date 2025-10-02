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
        Schema::create('nile_cruises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('vessel_type');
            $table->integer('capacity');
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->decimal('price_per_cabin', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('departure_location');
            $table->string('arrival_location');
            $table->text('itinerary')->nullable();
            $table->string('meal_plan')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('status')->default('available');
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('duration_nights');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
            $table->index(['vessel_type', 'capacity']);
            $table->index(['active', 'enabled']);
            $table->index(['price_per_person']);
            $table->index(['price_per_cabin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nile_cruises');
    }
};
