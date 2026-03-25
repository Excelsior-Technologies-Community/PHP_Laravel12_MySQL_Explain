<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryExplainController;

Route::get('/explain', [QueryExplainController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
