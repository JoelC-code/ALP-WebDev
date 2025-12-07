<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/board-create', [DashboardController::class, 'directCreate'])->name('board.create');
    Route::get('/board/{board}', [DashboardController::class, 'accessBoard'])->name('board.access');
});

//! FAST LOGIN FOR CHECKS
Route::get('/laika', function() {
    $user = User::where('email', 'laika@example.com')->first();

    Auth::login($user);

    return redirect('/dashboard');
});

require __DIR__.'/auth.php';
