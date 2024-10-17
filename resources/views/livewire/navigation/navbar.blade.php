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

        // return redirect('/');
    }

}; ?>

<div>
    <x-nav sticky full-width >
        <x-slot:brand>
            <label for="main-drawer" class="mr-3 lg:hidden">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:brand>

        <x-slot:actions>
            <span class="hidden lg:block">
                @if ($user = auth()->user())
                    <x-dropdown>
                        <x-slot:trigger>
                            <x-button label="{{ $user->name }}" class="btn-ghost" />
                        </x-slot:trigger>
                        <x-menu-item title="{{ __('Logout') }}" wire:click="logout" />
                    </x-dropdown>
                @else
                    <x-button label="{{ __('Login') }}" link="/login" class="btn-ghost" />
                @endif
            </span>
        </x-slot:actions>
    </x-nav>
</div>
