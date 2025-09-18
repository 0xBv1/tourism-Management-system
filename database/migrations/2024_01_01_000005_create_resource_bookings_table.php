<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resource_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_file_id')->constrained()->onDelete('cascade');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('occupied');
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['booking_file_id']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['start_date', 'end_date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_bookings');
    }
};




