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
        Schema::create('supplier_tour_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_tour_id')->constrained('supplier_tours')->onDelete('cascade');
            $table->foreignId('duration_id')->constrained('durations')->onDelete('cascade');
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate relationships
            $table->unique(['supplier_tour_id', 'duration_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_tour_durations');
    }
};
