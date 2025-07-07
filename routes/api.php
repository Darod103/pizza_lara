<?php

use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Registration\RegistrationController;
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


require __DIR__ . '/auth.php';
require __DIR__ . '/cart.php';
