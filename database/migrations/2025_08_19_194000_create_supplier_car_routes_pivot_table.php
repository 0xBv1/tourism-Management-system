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
        // Create the supplier_car_routes pivot table
        Schema::create('supplier_car_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('car_route_id')->constrained('car_routes')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['supplier_id', 'car_route_id']);
            
            // Indexes for better performance
            $table->index('supplier_id');
            $table->index('car_route_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_car_routes');
    }
}; 