<?php

use App\Http\Controllers\Api\Admin\OrderAdminController;
use App\Http\Controllers\Api\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    Route::delete('admin/order/{order}', [OrderAdminController::class, 'destroy']);
    Route::delete('admin/products/{product}', [ProductController::class, 'destroy']);
    Route::get('admin/order/', [OrderAdminController::class, 'index']);
    Route::post('admin/products/', [ProductController::class, 'store']);
    Route::patch('admin/products/{product}', [ProductController::class, 'update']);
    Route::put('admin/products/{product}', [ProductController::class, 'update']);
    Route::put('admin/order/{order}', [OrderAdminController::class, 'update']);
    Route::patch('admin/order/{order}', [OrderAdminController::class, 'update']);
});
