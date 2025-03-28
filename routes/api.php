<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ContentBlockController;
use App\Http\Controllers\FileController;
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
    'content-block'=> ContentBlockController::class,
    'attribute' => AttributeController::class,
    'file' => FileController::class,
  ]);

  Route::prefix('destroy')->group(function () {
    Route::post('user', [UserController::class, 'deleteMany']);
    Route::post('page', [PageController::class, 'deleteMany']);
    Route::post('template', [TemplateController::class, 'deleteMany']);
    Route::post('link', [LinkController::class, 'deleteMany']);
    Route::post('content-block', [ContentBlockController::class, 'deleteMany']);
    Route::post('attribute', [AttributeController::class, 'deleteMany']);
    Route::post('file', [FileController::class, 'deleteMany']);
  });
});
