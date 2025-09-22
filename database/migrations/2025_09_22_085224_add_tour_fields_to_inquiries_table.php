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
        Schema::table('inquiries', function (Blueprint $table) {
            // Rename name to guest_name for clarity
            $table->renameColumn('name', 'guest_name');
            
            // Add new tour-related fields
            $table->date('arrival_date')->nullable()->after('phone');
            $table->integer('number_pax')->nullable()->after('arrival_date');
            $table->string('tour_name')->nullable()->after('number_pax');
            $table->string('nationality')->nullable()->after('tour_name');
            
            // Add payment-related fields
            $table->decimal('total_amount', 10, 2)->nullable()->after('nationality');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_amount', 10, 2)->nullable()->after('paid_amount');
            $table->string('payment_method')->nullable()->after('remaining_amount');
            
            // Add inquiry_id field for custom ID format
            $table->string('inquiry_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Reverse the column rename
            $table->renameColumn('guest_name', 'name');
            
            // Drop the added columns
            $table->dropColumn([
                'inquiry_id',
                'arrival_date',
                'number_pax',
                'tour_name',
                'nationality',
                'total_amount',
                'paid_amount',
                'remaining_amount',
                'payment_method'
            ]);
        });
    }
};
