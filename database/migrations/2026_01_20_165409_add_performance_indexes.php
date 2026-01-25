<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->index(['list_id', 'position']);
        });

        Schema::table('board_members', function (Blueprint $table) {
            $table->index(['board_id', 'user_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex(['list_id', 'position']);
        });

        Schema::table('board_members', function (Blueprint $table) {
            $table->dropIndex(['board_id', 'user_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
