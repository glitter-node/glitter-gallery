<?php

use Illuminate\Support\Facades\Route;
use Modules\Glitter\Gallery\Http\Controllers\PublicArchiveController;
use Modules\Glitter\Gallery\Http\Controllers\PublicExhibitionController;
use Modules\Glitter\Gallery\Http\Controllers\PublicHomeController;
use Modules\Glitter\Gallery\Http\Controllers\PublicItemController;
use Modules\Glitter\Gallery\Http\Controllers\PublicRecordController;

Route::middleware(['optional.sanctum', 'throttle:600,1'])->group(function () {
    Route::get('home', PublicHomeController::class)->name('home');
    Route::get('exhibitions', [PublicExhibitionController::class, 'index'])->name('exhibitions.index');
    Route::get('exhibitions/{slug}', [PublicExhibitionController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('exhibitions.show');
    Route::get('items/{slug}', [PublicItemController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('items.show');
    Route::get('records', [PublicRecordController::class, 'index'])->name('records.index');
    Route::get('records/{slug}', [PublicRecordController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('records.show');
    Route::get('archive', [PublicArchiveController::class, 'index'])->name('archive.index');
    Route::get('archive/{category}', [PublicArchiveController::class, 'category'])->where('category', '[a-z0-9_\-]+')->name('archive.category');
    Route::get('archive/{category}/{slug}', [PublicArchiveController::class, 'show'])->where(['category' => '[a-z0-9_\-]+', 'slug' => '[a-z0-9\-]+'])->name('archive.materials.show');
});
