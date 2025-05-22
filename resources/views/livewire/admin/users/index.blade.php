<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.admin')]
#[Title('Users')]
class extends Component {

    use Toast, WithPagination;

    public string $search = '';

    public array $sortBy = ['column' => 'name', 'direction' => 'desc'];

    public string $role = 'all';

    public array $roles = [];

    public function delete(User $user): void
    {
        $user->delete();

        $this->success($user->name . __('deleted'));
    }

    // Definir les en-tetes de table.
    public function headers(): array
    {
        $headers = [
            ['key' => 'name', 'label' => __('Name')],
            ['key' => 'email', 'label' => __('Email')],
            ['key' => 'role', 'label' => __('Role')],
            ['key' => 'valid', 'label' => __('Valid')],
        ];

        if ('user' !== $this->role) {
            $headers = array_merge($headers, [
                ['key' => 'posts_count', 'label' => __('Posts')]
            ]);
        }

        return array_merge($headers, [
                ['key' => 'comments_count', 'label' => __('Comments')],
                ['key' => 'created_at', 'label' => __('Registration')]
            ]);
    }

    public function users(): LengthAwarePaginator
    {
        $query = User::query()
            ->when($this->search, fn (Builder $q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->role !== 'all', fn (Builder $q) => $q->where('role', $this->role))
            ->withCount('posts', 'comments')
            ->orderBy(...array_values($this->sortBy));

        $users = $query->paginate(10);

        $userCountsByRole = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');


        $totalUsers = $userCountsByRole->sum();

        $this->roles = collect([
            'all' => __('All') . "({$totalUsers})",
            'admin' => __('Administrators'),
            'redac' => __('Redactors'),
            'user' => __('Users')
        ])->map(function ($roleName, $roleId) use ($userCountsByRole) {
            $count = $userCountsByRole->get($roleId, 0);
            return [
                'name' => $roleId === 'all' ? $roleName : "{$roleName} ({$count})",
                'id' => $roleId
            ];
        })->values()->all();

        return $users;
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers()
        ];
    }

}; ?>

<div>
    <x-header separator progress-indicator>
        <x-slot:title>
            <a href="{{ route('admin') }}" title="{{ __('Back to Dashboard') }}">
                {{ __('Users') }}
            </a>
        </x-slot:title>
        <x-slot:middle class="!justify-end">
            <x-input icon='o-magnifying-glass' placeholder="{{ __('Search...') }}" wire:model.live='search'
                wire:change="$refresh" clearable />
        </x-slot:middle>
    </x-header>

    <x-radio label="{{ __('Role') }}" :options="$roles" placeholder-value='all' wire:model='role'
        wire:change='$refresh' />

    <br>

    <x-card>
        <x-table :headers="$headers" :rows="$users" striped :sort-by="$sortBy" link='/admin/users/{id}/edit'
            with-pagination>
            @scope('cell_name', $user)
            <x-avatar :image="$user->photo">
                <x-slot:title>
                    {{ $user->name }}
                </x-slot:title>
                <x-slot:subtitle>
                    {{ $user->role }}
                </x-slot:subtitle>
            </x-avatar>
            @endscope

            @scope('cell_valid', $user)
            @if ($user->valid)
            <x-icon name='o-check-circle' class='text-green-500' />
            @endif
            @endscope

            @scope('cell_role', $user)
            @if ($user->role === 'admin')
            <x-badge value="{{ __('Administrator') }}" class="badge-error" />
            @elseif ($user->role === 'redac')
            <x-badge value="{{ __('Redactor') }}" class="badge-warning" />
            @elseif ($user->role === 'user')
            {{ __('User') }}
            @endif
            @endscope

            @scope('cell_posts_count', $user)
            @if ($user->posts_count > 0)
            <x-badge value="{{ $user->posts_count }}" class="text-white badge-success" />
            @endif
            @endscope

            @scope('cell_comments_count', $user)
            @if ($user->comments_count > 0)
            <x-badge value="{{ $user->comments_count }}" class='text-white badge-primary' />
            @endif
            @endscope

            @scope('cell_created_at', $user)
            {{ $user->created_at->isoFormat('LL') }}
            @endscope

            @scope('actions', $user)
            <div class="flex">
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon="o-envelope" link="mailto:{{ $user->email }}" no-wire-navigate spinner
                            class="text-blue-500 btn-ghost btn-sm" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Send an email')
                    </x-slot:content>
                </x-popover>
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon='o-trash' wire:click="delete({{ $user->id }})"
                            class='text-red-500 btn-sm btn-ghost' confirm-text="Are you sure?"
                            wire:confirm="{{ __('Are you sure to delete this user?') }}" spinner="delete" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Delete')
                    </x-slot:content>
                </x-popover>
            </div>

            @endscope
        </x-table>
    </x-card>
</div>
