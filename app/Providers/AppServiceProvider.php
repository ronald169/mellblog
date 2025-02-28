<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Menu;
use Illuminate\View\View;
use Illuminate\Support\Facades;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Facades\View::composer(['components.layouts.app'], function (View $view) {
            $view->with(
                'menus',
                Menu::with(['submenus' => fn($query) => $query->orderBy('order') ])
                    ->orderBy('order')->get()
            );
        });
    }
}
