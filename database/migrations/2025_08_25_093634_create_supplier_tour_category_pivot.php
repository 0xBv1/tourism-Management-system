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
        if (!Schema::hasTable('supplier_tour_categories')) {
            Schema::create('supplier_tour_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_tour_id')->constrained('supplier_tours')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->unique(['supplier_tour_id', 'category_id'], 'stc_unique');
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
        Schema::dropIfExists('supplier_tour_categories');
    }
};
