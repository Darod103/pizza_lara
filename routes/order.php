<?php

use App\Http\Controllers\Api\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api',], function () {
   Route::apiResource('order',OrderController::class);
});
