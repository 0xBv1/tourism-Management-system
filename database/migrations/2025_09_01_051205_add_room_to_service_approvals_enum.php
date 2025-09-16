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
        // Add 'room' to the service_type enum
        DB::statement("ALTER TABLE service_approvals MODIFY COLUMN service_type ENUM('hotel', 'tour', 'trip', 'transport', 'room') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 'room' from the service_type enum
        DB::statement("ALTER TABLE service_approvals MODIFY COLUMN service_type ENUM('hotel', 'tour', 'trip', 'transport') NOT NULL");
    }
};
