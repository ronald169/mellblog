<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\{Auth, Session};

new class extends Component {

    public function logout(): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
        $this->redirect('/');
    }

}; ?>

<div>
    <x-menu activate-by-route>
        <x-menu-separator />

        <x-list-item :item="Auth::user()" value="name" sub-value="email" no-separator no-hover
            class="-mx-2 !-my-2 rounded">
            <x-slot:actions>
                <x-button icon="o-power" wire:click="logout" class="btn-circle btn-ghost btn-xs"
                    tooltip-left="{{ __('Logout') }}" no-wire-navigate />
            </x-slot:actions>
        </x-list-item>

        <x-menu-separator />

        <x-menu-item title="{{ __('Dashboard') }}" icon='o-building-office-2' link="{{ route('admin') }}" />
        <x-menu-item title="{{ __('Go on site') }}" icon='m-arrow-right-end-on-rectangle' link="/" />
        <x-menu-sub title="{{ __('Posts') }}" icon='s-document-text'>
            <x-menu-item title="{{ __('All posts') }}" link="{{ route('admin.posts.index') }}" />
            <x-menu-item title="{{ __('Add a post') }}" link="{{ route('admin.posts.create') }}" />
        </x-menu-sub>
        @if (Auth::user()->isAdmin())
        <x-menu-item title="{{ __('Categories') }}" icon='s-document-text'
            link="{{ route('admin.categories.index') }}" />
        @endif

        <x-menu-item>
            <x-theme-toggle />
        </x-menu-item>
    </x-menu>
</div>
