<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('notifications', function (Blueprint $table) {
        $table->uuid('board_id')->nullable()->after('user_id');
        $table->index('board_id');
    });
}

public function down()
{
    Schema::table('notifications', function (Blueprint $table) {
        $table->dropColumn('board_id');
    });
}

};
