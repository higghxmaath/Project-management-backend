<?php

use Illuminate\Support\Facades\Route;
require __DIR__.'/migrate.php';


Route::get('/', function () {
    return view('welcome');
});
