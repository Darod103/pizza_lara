<?php

use App\Http\Controllers\Api\Registration\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegistrationController::class);


require __DIR__ . '/auth.php';
require __DIR__ . '/product.php';
require __DIR__ . '/cart.php';
require __DIR__ . '/order.php';
