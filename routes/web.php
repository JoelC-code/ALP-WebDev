<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchBoard;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/board-create', [DashboardController::class, 'directCreate'])->name('board.create');
    Route::get('/board/{board}', [DashboardController::class, 'accessBoard'])->name('board.access');
    Route::get('/search-board', [SearchBoard::class, 'index'])->name('board.search');
});

//! FAST LOGIN FOR CHECKS
Route::get('/laika', function() {
    $user = User::where('email', 'laika@example.com')->first();

    Auth::login($user);

    return redirect('/dashboard');
});

Broadcast::routes(['middleware' => ['auth']]);

require __DIR__.'/auth.php';
require __DIR__.'/channels.php';
