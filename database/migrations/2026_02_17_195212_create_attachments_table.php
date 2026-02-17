<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('attachments', function (Blueprint $table) {
    $table->id();
    $table->uuid('card_id');
    $table->string('filename');
    $table->string('url');
    $table->timestamps();

    $table->foreign('card_id')
          ->references('id')
          ->on('cards')
          ->cascadeOnDelete();
});

}

};
