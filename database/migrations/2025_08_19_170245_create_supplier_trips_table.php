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
            $table->index(['trip_type', 'enabled']);
            $table->index('travel_date');
            $table->index('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_trips');
    }
};
