<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ContentBlockController;
use App\Http\Controllers\DataCollectionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

$resources = [
  'user' => UserController::class,
  'page' => PageController::class,
  'template' => TemplateController::class,
  'link' => LinkController::class,
  'content-block' => ContentBlockController::class,
  'attribute' => AttributeController::class,
  'file' => FileController::class,
  'data-collection' => DataCollectionController::class,
];


Route::prefix('admin')->middleware(['auth:sanctum', 'role:root'])->group(function () use($resources) {
  Route::get('me', [UserController::class, 'me']);

  Route::apiResources($resources);
  
  Route::prefix('destroy')->group(function () use ($resources) {
    foreach ($resources as $route => $controller) {
      Route::post($route, [$controller, 'deleteMany']);
    }
  });
});
