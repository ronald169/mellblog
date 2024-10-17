<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'index');
Volt::route('/category/{slug}', 'index');

Route::middleware('guest')->group(function () {
    Volt::route('/register', 'auth.register')->name('register');
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('forgot.password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});
