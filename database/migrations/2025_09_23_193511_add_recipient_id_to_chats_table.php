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
        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->index(['inquiry_id', 'recipient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropIndex(['inquiry_id', 'recipient_id', 'created_at']);
            $table->dropColumn('recipient_id');
        });
    }
};
