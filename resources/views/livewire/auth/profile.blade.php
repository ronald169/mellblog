<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\{Hash, Auth};
use Livewire\Attributes\{Title, Layout, Validate};
use Illuminate\Support\Str;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

new
#[Title('Profile')] #[Layout('components.layouts.auth')]
class extends Component {

    use Toast, WithFileUploads;

    public User $user;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Validate('nullable|image|max:1024')]
    public $photo;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->email = $this->user->email;
    }

    public function save(): void
    {
        $data = $this->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'password' => 'nullable|confirmed|min:8'
        ]);

        if(empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $this->user->update($data);

        if ($this->photo) {

            $url = $this->photo->store('profile', 'public');

            $this->user->update(['photo' => "/storage/$url"]);
        }

        $this->success(__('Profile update with success.'), redirectTo: '/profile');
    }

    public function deleteAccount(): void
    {
        $this->user->delete();
        $this->success(__('Account deleted with success.'), redirectTo: '/');
    }

    public function generatePassword($length = 16): void
    {
        $this->password = Str::random($length);
        $this->password_confirmation = $this->password;
    }

}; ?>

<div>
    <x-card class="flex  items-center justify-center h-">
        <a href="/" title="{{ __('Go on site') }}">
            <x-card class="items-center  py-0" title="{{ __('Update profile') }}" shadow separator progress-indicator>
            </x-card>
        </a>


        <x-form wire:submit="save" class='mt-10'>

            <x-file label="{{ __('Photo') }}" wire:model='photo' accept="image/jpg, image/png" crop-after-change>
                <img src="{{ $user->photo ?? '/profile-empty.jpg' }}" alt="Profile photo" class="h-40 rounded-lg">
            </x-file>

            <x-input label="{{ __('E-mail') }}" wire:model='email' icon='o-envelope' inline />
            <hr />
            <x-input label="{{ __('Password') }}" wire:model='password' icon='o-key' inline />
            <x-input label="{{ __('Password confirmation') }}" wire:model='password_confirmation' icon='o-key' inline />

            <x-button label="{{ __('Generate a secure password') }}" wire:click='generatePassword()' icon='o-wrench'
                class='btn-outline btn-sm' />

            <x-slot:actions>
                <x-button label="{{ __('Cancel') }}" link='/' class='btn-ghost' />
                <x-button label="{{ __('Delete account') }}" icon='o-hand-thumb-down'
                    wire:confirm="{{ __('Are you sure to delete this account?') }}" wire:click="deleteAccount"
                    class='btn-warning' />
                <x-button label="{{ __('Save') }}" icon='o-paper-airplane' type="submit" class='btn-primary' />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>