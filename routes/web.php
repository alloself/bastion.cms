<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('sitemap.xml', [SiteController::class, 'sitemap'])->name('site.sitemap');
//Route::get('{any?}', [SiteController::class, 'show'])->where('any', '.*')->name('site.show');
