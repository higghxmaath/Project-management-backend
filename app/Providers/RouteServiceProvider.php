<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['board_id', 'created_at']);
        });

        Schema::table('board_members', function (Blueprint $table) {
            $table->index(['board_id', 'user_id']);
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->index(['list_id', 'position']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down()
    {
        //
    }
};
