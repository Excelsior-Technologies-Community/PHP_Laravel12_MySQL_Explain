<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryExplainController;

// Product Dashboard
Route::get('/explain', [QueryExplainController::class, 'index']);
Route::post('/product/store', [QueryExplainController::class, 'store'])->name('product.store');
Route::delete('/product/{id}', [QueryExplainController::class, 'destroy'])->name('product.delete');

// Feature 1: Visual Query Execution Plan
Route::get('/visual-explain', [QueryExplainController::class, 'visualExplain'])->name('visual.explain');

// Feature 2: Auto-Index Recommendation
Route::get('/index-recommend', [QueryExplainController::class, 'indexRecommend'])->name('index.recommend');

// Feature 3: Performance History
Route::get('/performance-history', [QueryExplainController::class, 'performanceHistory'])->name('performance.history');

// Feature 4: Schema Analyzer
Route::get('/schema-analyzer', [QueryExplainController::class, 'schemaAnalyzer'])->name('schema.analyzer');

Route::get('/', function () {
    return redirect('/explain');
});
