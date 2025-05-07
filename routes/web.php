<?php

use App\Http\Middleware\IsAdminOrRedac;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'index');
Volt::route('/category/{category:slug}', 'index');
Volt::route('/posts/{post:slug}', 'posts.show')->name('posts.show');
Volt::route('/search/{param}', 'index')->name('posts.search');
Volt::route('/pages/{page:slug}', 'pages.show')->name('pages.show');
Volt::route('/favorites', 'index')->name('posts.favorites');

Route::middleware('guest')->group(function () {
    Volt::route('/register', 'auth.register')->name('register');
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('forgot.password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('/profile', 'auth.profile')->name('profile');

    Route::middleware(IsAdminOrRedac::class)->group(function () {
        Volt::route('/admin/dashboard', 'admin.index')->name('admin');
        Volt::route('/admin/posts/index', 'admin.posts.index')->name('admin.posts.index');
        Volt::route('/admin/posts/create', 'admin.posts.create')->name('admin.posts.create');
    });
});
