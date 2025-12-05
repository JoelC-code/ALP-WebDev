<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/board/create', \App\Livewire\Board\CreateBoard::class);
    Route::get('/board/{board}', \App\Livewire\Board\ViewBoard::class);
    Route::delete('/board/{board}', \App\Livewire\Board\DeleteBoard::class);
    Route::get('/dashboard', \App\Livewire\Board\ListBoard::class);
});

require __DIR__.'/auth.php';
