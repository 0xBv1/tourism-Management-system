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
        Schema::create('supplier_room_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_room_id');
            $table->string('locale');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            
            $table->unique(['supplier_room_id', 'locale']);
            $table->foreign('supplier_room_id')->references('id')->on('supplier_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_room_translations');
    }
};
