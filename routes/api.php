<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GalleryController;

Route::get('/gallery/images', [GalleryController::class, 'getImages']);
Route::post('/gallery/upload', [GalleryController::class, 'upload']);