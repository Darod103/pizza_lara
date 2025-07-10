<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Registration\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegistrationController::class);
Route::post('auth/login', [AuthController::class, 'login']);


require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/product.php';
require __DIR__ . '/cart.php';
require __DIR__ . '/order.php';
