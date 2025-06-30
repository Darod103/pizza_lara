<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Registration\RegistrationController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegistrationController::class);

Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'products',
    'as' => 'products.',
], function () {
    Route::apiResource('',ProductController::class)->except(['index', 'show']);
});

Route::group(['middleware' => 'auth:api','prefix'=>'cart'], function () {
    Route::apiResource('/',CartController::class)->except(['update', 'destroy']);
    //TODO узнать правильно ли так делать....
    Route::put('item/{item}',[CartController::class,'update']);
    Route::patch('item/{item}',[CartController::class,'update']);
    Route::delete('item/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/{cart}', [CartController::class, 'destroyAll']);

});

require __DIR__ . '/auth.php';
