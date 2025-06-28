<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Registration\RegistrationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegistrationController::class);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::group(['middleware'=>'auth:api','prefix'=>'products'], function () {
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});

Route::group(['middleware'=>'auth:api','prefix'=>'cart'], function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/items', [CartController::class, 'addItem']);
    Route::put('/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('/items/{itemId}', [CartController::class, 'removeItem']);
    Route::delete('/clear', [CartController::class, 'clear']);
});

require __DIR__ . '/auth.php';
