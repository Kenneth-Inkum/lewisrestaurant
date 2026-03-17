<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::livewire('/', 'landing-page')->name('home');
Route::livewire('/menu', 'pages::menu')->name('menu');
Route::livewire('/contact', 'pages::contact')->name('contact');
Route::livewire('/reservations', 'pages::reservations')->name('reservations');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin
    Route::livewire('/admin/menu', 'admin::menu-manager')->name('admin.menu');
    Route::livewire('/admin/reservations', 'admin::reservations-manager')->name('admin.reservations');
    Route::livewire('/admin/tables', 'admin::tables-manager')->name('admin.tables');
    Route::livewire('/admin/orders', 'admin::orders-manager')->name('admin.orders');
});

require __DIR__.'/settings.php';
