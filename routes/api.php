<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);

