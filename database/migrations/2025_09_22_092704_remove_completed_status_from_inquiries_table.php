<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, update any existing 'completed' status to 'confirmed'
        DB::table('inquiries')
            ->where('status', 'completed')
            ->update(['status' => 'confirmed']);
        
        // Then modify the enum to remove 'completed' status using raw SQL
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore the 'completed' status to the enum using raw SQL
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
    }
};
