<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\ProductController;

Route::get('/cashier/products', [ProductController::class, 'index'])->name('cashier.products.index');
Route::post('/cashier/products', [ProductController::class, 'store'])->name('cashier.products.store'); // ADD THIS
Route::get('/cashier/products/{id}', [ProductController::class, 'show'])->name('cashier.products.show');
Route::put('/cashier/products/{id}', [ProductController::class, 'update'])->name('cashier.products.update');
Route::delete('/cashier/products/{id}', [ProductController::class, 'destroy'])->name('cashier.products.destroy');