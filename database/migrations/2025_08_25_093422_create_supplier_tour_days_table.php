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
        Schema::create('supplier_tour_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_tour_id')->constrained('supplier_tours')->onDelete('cascade');
            $table->unsignedSmallInteger('day_number')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_tour_days');
    }
};
