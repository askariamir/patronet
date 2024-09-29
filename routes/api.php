<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::put('/brands/{id}', [BrandController::class, 'update']);
Route::post('/brands', [BrandController::class, 'store']);
// Get all brands
Route::get('/brands', [BrandController::class, 'index']);

// Get a single brand by ID
Route::get('/brands/{id}', [BrandController::class, 'show']);


Route::delete('/brands/{id}', [BrandController::class, 'destroy']);

// Delete a single product
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

