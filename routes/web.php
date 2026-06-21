<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('products', \App\Livewire\ProductManager::class)
    ->middleware(['auth', 'verified'])
    ->name('products');

Route::get('stock-logs', \App\Livewire\StockLogIndex::class)
    ->middleware(['auth', 'verified'])
    ->name('stock-logs');

require __DIR__.'/auth.php';
