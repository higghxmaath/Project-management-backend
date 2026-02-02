<?php

use Illuminate\Support\Facades\Route;
require __DIR__.'/migrate.php';


Route::get('/', function () {
    return view('welcome');
});

Route::get('/__migrate', function () {
    \Artisan::call('migrate', ['--force' => true]);
    return nl2br(\Artisan::output());
});
