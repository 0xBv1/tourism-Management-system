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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->enum('trip_type', ['one_way', 'round_trip']);
            $table->foreignId('departure_city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->foreignId('arrival_city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->date('travel_date');
            $table->date('return_date')->nullable();
            $table->datetime('departure_time');
            $table->datetime('arrival_time');
            $table->decimal('seat_price', 10, 2);
            $table->integer('total_seats');
            $table->integer('available_seats');
            $table->text('additional_notes')->nullable();
            $table->text('amenities')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('trip_name');
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
        Schema::dropIfExists('trips');
    }
};
