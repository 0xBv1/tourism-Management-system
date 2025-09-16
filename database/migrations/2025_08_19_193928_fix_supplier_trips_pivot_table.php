<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the existing table with trip data fields
        Schema::dropIfExists('supplier_trips');
        
        // Recreate as a proper pivot table
        Schema::create('supplier_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['supplier_id', 'trip_id']);
            
            // Indexes for better performance
            $table->index('supplier_id');
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the pivot table
        Schema::dropIfExists('supplier_trips');
        
        // Recreate the original table structure
        Schema::create('supplier_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('trip_name');
            $table->enum('trip_type', ['one_way', 'round_trip', 'special_discount']);
            $table->string('departure_city');
            $table->string('arrival_city');
            $table->date('travel_date');
            $table->date('return_date')->nullable();
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->decimal('seat_price', 10, 2);
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->text('additional_notes')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'enabled']);
            $table->index(['departure_city', 'arrival_city'], 'trips_city_idx');
            $table->index('travel_date');
            $table->index('approved');
        });
    }
};
