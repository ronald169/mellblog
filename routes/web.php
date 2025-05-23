<?php

use App\Http\Middleware\IsAdmin;
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
        Volt::route('/admin/posts/{post:slug}/edit', 'admin.posts.edit')->name('admin.posts.edit');
        Volt::route('/admin/posts/{post:slug}/edit', 'admin.posts.edit')->name('admin.posts.edit');
    });

    Route::middleware(IsAdmin::class)->prefix('admin')->group(function () {
        Volt::route('/categories/index', 'admin.categories.index')->name('admin.categories.index');
        Volt::route('/categories/{category}/edit', 'admin.categories.edit')->name('admin.categories.edit');
        Volt::route('/pages/index', 'admin.pages.index')->name('admin.pages.index');
        Volt::route('/pages/create', 'admin.pages.create')->name('admin.pages.create');
        Volt::route('/pages/{page:slug}/edit', 'admin.pages.edit')->name('admin.pages.edit');
        Volt::route('/users/index', 'admin.users.index')->name('admin.users.index');
        Volt::route('/users/{user}/edit', 'admin.users.edit')->name('admin.users.edit');

    });
});
