<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\ProductController;

// Remove the prefix - make it accessible from dashboard
Route::get('/cashier/products', [ProductController::class, 'index'])->name('cashier.products.index');
Route::get('/cashier/products/{id}', [ProductController::class, 'show'])->name('cashier.products.show');