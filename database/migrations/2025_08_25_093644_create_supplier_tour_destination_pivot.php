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
        if (!Schema::hasTable('supplier_tour_destinations')) {
            Schema::create('supplier_tour_destinations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_tour_id')->constrained('supplier_tours')->onDelete('cascade');
                $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
                $table->unique(['supplier_tour_id', 'destination_id'], 'std_unique');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_tour_destinations');
    }
};
