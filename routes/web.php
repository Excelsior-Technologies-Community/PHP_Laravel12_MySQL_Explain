<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryExplainController;

Route::get('/explain', [QueryExplainController::class, 'index']);
Route::post('/product/store', [QueryExplainController::class, 'store'])->name('product.store');
Route::delete('/product/{id}', [QueryExplainController::class, 'destroy'])->name('product.delete');

Route::get('/', function () {
    return redirect('/explain');
});
