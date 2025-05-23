<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\{Auth, Session};
use Illuminate\Support\Collection;

new class extends Component {

    public Collection $menus;

    public function mount(): void
    {
        $this->fill($this->menus);
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->redirect('/');
    }

}; ?>

<div>
    <x-nav sticky full-width>
        <x-slot:brand>
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:brand>

        <x-slot:actions>
            <livewire:search />
            @auth
            @if (auth()->user()->favoritePosts()->exists())
            <a title="{{ __('Favorites posts') }}" href="{{ route('posts.favorites') }}">
                <x-icon name="s-star" class="w-7 h-7" />
            </a>
            @endif
            @endauth
            <x-theme-toggle title="{{ __('Toggle theme') }}" class="w-4 h-8" />

            @foreach ($menus as $menu)
            @if ($menu->submenus->isNotEmpty())
            <x-dropdown>
                <x-slot:trigger>
                    <x-button label="{{ $menu->label }}" class="btn-ghost" />
                </x-slot:trigger>
                @foreach ($menu->submenus as $submenu)
                <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}" style="min-width: max-content" />
                @endforeach
            </x-dropdown>
            @else
            <x-button label="{{ $menu->label }}" link="{{ $menu->link }}"
                :external="Str::startsWith($menu->link, 'http')" class="btn-ghost" />

            @endif
            @endforeach

            <span class="hidden lg:block">
                @if ($user = auth()->user())
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button label="{{ $user->name }}" class="btn-ghost" />
                    </x-slot:trigger>
                    <x-menu-item title="{{ __('Profile') }}" link="{{ route('profile') }}" />
                    <x-menu-item title="{{ __('Admin') }}" link="{{ route('admin') }}" />
                    <x-menu-item title="{{ __('Logout') }}" wire:click="logout" />
                </x-dropdown>
                @else
                <x-button label="{{ __('Login') }}" link="/login" class="btn-ghost" />
                @endif
            </span>
        </x-slot:actions>
    </x-nav>
</div>
