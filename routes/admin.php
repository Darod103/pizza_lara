<?php

use App\Http\Controllers\Api\Admin\OrderAdminController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api','role:admin']], function () {
    Route::delete('admin/order/{order}', [OrderAdminController::class,'destroy']);
});
