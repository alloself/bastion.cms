<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/{any?}', [SiteController::class, 'admin'])
    ->where('any', '.*')
    ->name('admin');

// Маршрут для robots.txt
Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');

Route::get('/{path?}', [SiteController::class, 'site'])
    ->where('path', '^((?!public\/).)*$')
    ->name('site');
