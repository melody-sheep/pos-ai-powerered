<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SelectRoleController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Role selection route (must be before auth routes)
Route::get('/select-role', [SelectRoleController::class, 'create'])
    ->name('select-role')
    ->middleware('guest');

// Dashboard route - UPDATED to use cashier dashboard
Route::get('/dashboard', function () {
    return view('cashier.dashboard-cdn'); // Use the CDN version
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test route
Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        return response()->json([
            'success' => true,
            'message' => 'Connected to database successfully!',
            'database' => $dbName,
            'tables' => \Illuminate\Support\Facades\DB::select('SHOW TABLES'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Temporary logout route for testing
Route::get('/get-logout', function() {
    \Auth::logout();
    \Session::flush();
    return redirect('/login');
});

// Add this right before require __DIR__.'/auth.php';
Route::get('/test-design', function() {
    return view('test-design');
});

Route::get('/test-login-page', function() {
    return view('test-login');
});

// This line loads all auth routes (login, register, etc.)
require __DIR__.'/auth.php';