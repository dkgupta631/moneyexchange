<?php
use Illuminate\Support\Facades\Route;

use Inertia\inertia;

Route::get('/', function () {
    return inertia('Home');
});
