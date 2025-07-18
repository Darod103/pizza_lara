<?php

use App\Http\Controllers\Api\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class)->only(['index', 'show']);
