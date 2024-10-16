<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'users.index');

Route::middleware('guest')->group(function () {
    Volt::route('/register', 'auth.register')->name('register');
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('forgot-password');
    Volt::route('/reset-password', 'auth.reset-password')->name('reset-password');
});
