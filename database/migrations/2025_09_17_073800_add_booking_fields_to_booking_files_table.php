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
        Schema::table('booking_files', function (Blueprint $table) {
            $table->text('checklist')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_files', function (Blueprint $table) {
            $table->dropColumn(['checklist', 'notes', 'total_amount', 'currency']);
        });
    }
};
