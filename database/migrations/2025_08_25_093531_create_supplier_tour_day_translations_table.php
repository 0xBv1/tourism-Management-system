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
        if (!Schema::hasTable('supplier_tour_day_translations')) {
            Schema::create('supplier_tour_day_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_tour_day_id')->constrained('supplier_tour_days')->onDelete('cascade');
                $table->string('locale')->index();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->unique(['supplier_tour_day_id', 'locale'], 'std_day_locale_unique');
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
        Schema::dropIfExists('supplier_tour_day_translations');
    }
};
