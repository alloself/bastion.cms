<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:sanctum', 'role:root'])->group(function () {
  Route::get('/me', [UserController::class, 'me']);

  Route::apiResources([
    'user' => UserController::class,
    'page' => PageController::class,
    'template' => TemplateController::class,
    'link'=> LinkController::class,
  ]);

  Route::prefix('destroy')->group(function () {
    Route::post('user', [UserController::class, 'deleteMany']);
    Route::post('page', [PageController::class, 'deleteMany']);
    Route::post('template', [TemplateController::class, 'deleteMany']);
    Route::post('link', [LinkController::class, 'deleteMany']);
  });
});
