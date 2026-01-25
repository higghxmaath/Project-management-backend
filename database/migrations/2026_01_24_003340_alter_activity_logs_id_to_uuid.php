<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        // 1. Remove auto_increment first
        DB::statement('ALTER TABLE activity_logs MODIFY id BIGINT NOT NULL');

        // 2. Drop primary key
        DB::statement('ALTER TABLE activity_logs DROP PRIMARY KEY');

        // 3. Change column to UUID string
        DB::statement('ALTER TABLE activity_logs MODIFY id CHAR(36) NOT NULL');

        // 4. Re-add primary key
        DB::statement('ALTER TABLE activity_logs ADD PRIMARY KEY (id)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE activity_logs DROP PRIMARY KEY');
        DB::statement('ALTER TABLE activity_logs MODIFY id BIGINT NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE activity_logs ADD PRIMARY KEY (id)');
    }
};
