<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;
use App\Models\User;

new
#[Layout('components.layouts.admin')]
#[Title('Edit User')]
class extends Component {

    use Toast;

    public User $user;

    public string $name = '';
    public string $email = '';
    public string $role = '';
    public bool $valid = false;
    public bool $isStudent;

    public function mount(): void
    {
        $this->fill($this->user);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'role' => ['required', Rule::in(['admin', 'user', 'redac'])],
            'valid' => ['required', 'boolean'],
        ]);

        $this->user->update($data);

        $this->success(__('User edited with success.'), redirectTo: '/admin/users/index');
    }

    public function with(): array
    {
        return [
            'roles' => [
                ['name' => __('Administrator'), 'id' => 'admin'],
                ['name' => __('User'), 'id' => 'user'],
                ['name' => __('Redactor'), 'id' => 'redac'],
            ],
        ];
    }

}; ?>

<div>
    <x-header title="{{ __('Edit an account') }}" separator progress-indicator>
        <x-slot:actions>
            <x-button link="{{ route('admin') }}" icon="s-building-office-2" label="{{ __('Dashboard') }}"
                class='btn-outline lg:hidden' />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-form wire:submit='save'>
            <x-input label="{{ __('Name') }}" wire:model='name' icon='o-user' inline />
            <x-input label="{{ __('Email') }}" wire:model='email' icon='o-envelope' inline />

            <br>

            <x-radio label="{{ __('User role') }}" :options="$roles" wire:model='role' inline />
            <x-toggle label="{{ __('Valid') }}" wire:model='valid' inline />

            <x-slot:actions>
                <div class="text-left">
                    <x-button link="{{ route('admin.users.index') }}" icon="s-x-mark" label="{{ __('Cancel') }}"
                        class='btn-outline' />
                    <x-button type='submit' icon='o-paper-airplane' label="{{ __('Save') }}" class="btn-primary"
                        spinner='save' />

                </div>
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
