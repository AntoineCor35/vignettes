<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Models\Card;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les cartes
    Route::resource('cards', CardController::class)->except(['show']);
});

Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');

// Routes administrateur
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update.role');

    // Gestion des catégories
    Route::resource('categories', CategoryController::class);
});

Route::get('/landing', function () {
    $cards = Card::with('media', 'category')
        ->where('deleted', false)
        ->latest()
        ->take(60)
        ->get();
    return view('landing', compact('cards'));
})->name('landing');

require __DIR__ . '/auth.php';
