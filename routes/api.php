<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:sanctum', 'role:root'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);

    Route::apiResources([
        'user' => UserController::class,      
    ]);

});