<?php


use App\Http\Controllers\Api\Cart\CartController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api','prefix'=>'cart'], function () {
    Route::apiResource('/',CartController::class)->except(['update', 'destroy']);
    Route::put('item/{item}',[CartController::class,'update']);
    Route::patch('item/{item}',[CartController::class,'update']);
    Route::delete('item/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/{cart}', [CartController::class, 'destroyAll']);

});
