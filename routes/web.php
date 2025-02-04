<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('sitemap.xml', [SiteController::class, 'sitemap'])->name('site.sitemap');
//Route::get('{any?}', [SiteController::class, 'show'])->where('any', '.*')->name('site.show');

Route::get('/admin{any}', [SiteController::class, 'admin'])->where('any', '.*')->name('admin');
Route::get('/{path?}', [SiteController::class, 'web'])->where('path', '^((?!public\/).)*$.*')->name('web');