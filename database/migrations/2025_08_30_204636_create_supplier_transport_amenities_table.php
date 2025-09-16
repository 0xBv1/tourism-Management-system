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
        Schema::create('supplier_transport_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_transport_id')->constrained('supplier_transports')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate relationships
            $table->unique(['supplier_transport_id', 'amenity_id'], 'supplier_transport_amenity_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_transport_amenities');
    }
};
