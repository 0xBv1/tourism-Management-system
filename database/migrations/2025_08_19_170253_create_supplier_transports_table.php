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
        Schema::create('supplier_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('origin_location');
            $table->string('destination_location');
            $table->text('intermediate_stops')->nullable();
            $table->integer('estimated_travel_time'); // in minutes
            $table->decimal('distance', 8, 2)->nullable();
            $table->string('route_type');
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('EGP');
            $table->string('vehicle_type')->nullable();
            $table->integer('seating_capacity')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'enabled']);
            $table->index(['route_type', 'enabled']);
            $table->index('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_transports');
    }
};
