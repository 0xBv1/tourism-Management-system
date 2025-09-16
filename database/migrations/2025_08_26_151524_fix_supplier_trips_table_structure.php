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
        // Drop the current pivot table structure
        Schema::dropIfExists('supplier_trips');
        
        // Recreate as a full trips table
        Schema::create('supplier_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('trip_name', 191);
            $table->enum('trip_type', ['one_way', 'round_trip', 'special_discount']);
            $table->string('departure_city', 191);
            $table->string('arrival_city', 191);
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
            $table->string('featured_image', 191)->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'enabled']);
            $table->index(['trip_type', 'enabled']);
            $table->index('travel_date');
            $table->index('approved');
            $table->index('departure_city');
            $table->index('arrival_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the full trips table
        Schema::dropIfExists('supplier_trips');
        
        // Recreate as a pivot table
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
};
