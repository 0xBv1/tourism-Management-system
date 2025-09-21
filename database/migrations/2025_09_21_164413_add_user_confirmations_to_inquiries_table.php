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
            $table->text('user_confirmations')->nullable();
            $table->timestamp('user1_confirmed_at')->nullable();
            $table->timestamp('user2_confirmed_at')->nullable();
            $table->unsignedBigInteger('user1_id')->nullable();
            $table->unsignedBigInteger('user2_id')->nullable();
        });
        
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['user1_id']);
            $table->dropForeign(['user2_id']);
            $table->dropColumn([
                'user_confirmations',
                'user1_confirmed_at',
                'user2_confirmed_at',
                'user1_id',
                'user2_id'
            ]);
        });
    }
};
