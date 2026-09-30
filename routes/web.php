<?php

use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GiftCategoryController;
use App\Http\Controllers\GiftItemController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/confirmar', 'Confirm')->name('confirmar');
Route::post('/confirmar', [ConfirmationController::class, 'store'])->name('confirmar.store');
Route::get('/presentes/dados', [GiftItemController::class, 'data'])->name('presentes.data');
Route::get('/presentes', [GiftItemController::class, 'index'])->name('presentes');

Route::redirect('confirmados', '/dashboard');
Route::redirect('painel/categorias', '/categories');
Route::redirect('painel/presentes', '/gifts');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('categories', [GiftCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [GiftCategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{giftCategory}', [GiftCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('gifts', [GiftItemController::class, 'admin'])->name('gifts.index');
    Route::post('gifts', [GiftItemController::class, 'store'])->name('gifts.store');
    Route::delete('gifts/{giftItem}', [GiftItemController::class, 'destroy'])->name('gifts.destroy');
});

require __DIR__.'/settings.php';
