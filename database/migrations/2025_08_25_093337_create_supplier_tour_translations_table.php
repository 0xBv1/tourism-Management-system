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
        Schema::create('supplier_tour_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_tour_id')->constrained('supplier_tours')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title');
            $table->text('overview')->nullable();
            $table->text('highlights')->nullable();
            $table->text('excluded')->nullable();
            $table->text('included')->nullable();
            $table->string('duration')->nullable();
            $table->string('type')->nullable();
            $table->string('run')->nullable();
            $table->string('pickup_time')->nullable();
            $table->unique(['supplier_tour_id', 'locale']);
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
        Schema::dropIfExists('supplier_tour_translations');
    }
};
