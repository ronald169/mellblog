<?php

use Livewire\Volt\Component;
use App\Models\{Post, Category};
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Title};
use Mary\Traits\Toast;
use Livewire\WithPagination;

new
#[Title('List Posts')]
#[Layout('components.layouts.admin')]
class extends Component {

    use Toast, WithPagination;

    public string $search = '';
    public int $category_id = 0;
    public Collection $categories;
    public array $sortBy = ['column' => 'updated_at', 'direction' => 'desc'];
    public bool $drawer = false;

    public function mount(): void
    {
        $this->categories = $this->getCategories();
    }

    public function getCategories(): Collection
    {
        if (Auth::user()->isAdmin()) {
            return Category::all();
        }

        //return Category::whereHas('posts', fn (Builder $q) => $q->where('user_id', Auth::id()))->get();
        return Category::whereRelation('posts', 'user_id', Auth::id())->get();
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'title', 'label' => __('Title')],
        ];

        if (Auth::user()->isAdmin()) {
            $headers = array_merge($headers, [
                ['key' => 'user_name', 'label' => __('Author')]
            ]);
        }

        return array_merge($headers, [
            ['key' => 'category_title', 'label' => __('Category')],
            ['key' => 'comments_count', 'label' => __('')],
            ['key' => 'active', 'label' => __('Published')],
            ['key' => 'date', 'label' => __('Date')],
        ]);
    }

    public function posts()
    {
        return Post::query()
            ->select('id', 'title', 'slug', 'category_id', 'active', 'user_id', 'created_at', 'updated_at')
            ->when(Auth::user()->isAdmin(), fn (Builder $q) => $q->withAggregate('user', 'name'))
            ->when(!Auth::user()->isAdmin(), fn (Builder $q) => $q->where('user_id', Auth::id()))
            ->when($this->search, fn (Builder $q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->category_id, fn (Builder $q) => $q->where('category_id', $this->category_id))
            ->withAggregate('category', 'title')
            ->withCount('comments')
            //->when('date' === $this->sortBy['column'], fn (Builder $q) => $q->orderBy('created_at', $this->sortBy['direction']), fn (Builder $q) => $q->orderBy($this->sortBy['column'], $this->sortBy['direction']))
            ->orderBy(...array_values($this->sortBy))
            ->latest()
            ->paginate(6);
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            $this->resetPage();
        }
    }

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function deletePost(int $id): void
    {
        $post = Post::findOrFail($id);

        $post->delete();

        $this->success("$post->name " . __('delete'));
    }

    public function clonePost(int $postId): void
    {
        $originalPost = Post::findOrFail($postId);

        $clonedPost = $originalPost->replicate();

        $postRepository = new PostRepository();

        $clonedPost->slug = $postRepository->generateUniqueSlug($originalPost->slug);

        $clonedPost->active = false;

        $clonedPost->save();

        // Redirected to cloned post
    }

    public function with(): array
    {
        return [
            'posts' => $this->posts(),
            'headers' => $this->headers(),
        ];
    }

}; ?>

<div>
    <x-header title="{{ __('Posts') }}" separator progress-indicator>
        <x-slot:actions>
            <x-button label="{{ __('Add a post') }}" class='btn-outline lg:hidden' link="#" />
            <x-button label="{{ __('Dashboard') }}" icon='s-building-office-2' class='btn-outline lg:hidden'
                link="{{ route('admin') }}" />
            <x-input placeholder="{{ __('Search...') }}" wire:model.live.debounce='search' clearable
                icon='o-magnifying-glass' />

            <x-popover>
                <x-slot:trigger>
                    <x-button label="{{ __('Filters') }}" icon='o-funnel' wire:click="drawer = true"
                        class='btn-outline' />
                </x-slot:trigger>

                <x-slot:content class='pop-small'>
                    @lang('Filters')
                </x-slot:content>
            </x-popover>

            <x-drawer title="{{ __('Filters') }}" wire:model='drawer' right separator with-close-button>
                <x-select label="{{ __('Category') }}" :options="$categories"
                    placeholder="{{ __('Select a category') }}" placeholder-value='0' option-label='title'
                    wire:model.live='category_id' wire:change="$refresh" />

            </x-drawer>


        </x-slot:actions>
    </x-header>

    @if ($posts->count() > 0)
    <x-card>
        <x-table stripped :headers="$headers" :rows="$posts" link="#" with-pagination :sort-by="$sortBy">
            @scope('header_comments_count', $header)
            {{ $header['label'] }}
            <x-icon name='c-chat-bubble-left' />
            @endscope

            @scope('cell_user.name', $post)
            {{ $post->user->name }}
            @endscope

            @scope('cell_category.title', $post)
            {{ $post->category->title }}
            @endscope

            @scope('cell_comments_count', $post)
            @if ($post->comments_count > 0)
            <x-badge value="{{ $post->comments_count }}" class='badge-primary' />
            @endif
            @endscope

            @scope('cell_active', $post)
            @if ($post->active)
            <x-icon name='o-check-circle' />
            @endif
            @endscope

            @scope('cell_date', $post)
            @lang('Created') {{ $post->created_at->diffForHumans() }}
            @if ($post->updated_at != $post->created_at)
            <br>
            @lang('Updated') {{ $post->updated_at->diffForHumans() }}
            @endif
            @endscope

            @scope('actions', $post)
            <div class="flex">
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon='o-finger-print' wire:click="clonePost({{ $post->id }})" spinner
                            class='btn-ghost btn-sm' />
                    </x-slot:trigger>

                    <x-slot:content class='pop-small'>
                        @lang('Clone')
                    </x-slot:content>
                </x-popover>

                <x-popover>
                    <x-slot:trigger>
                        <x-button icon='o-trash' wire:click="deletePost({{ $post->id }})"
                            wire:confirm="{{ __('Are you sure to delete this post?') }}" spinner
                            class='text-red-500 btn-ghost btn-sm' />
                    </x-slot:trigger>

                    <x-slot:content class="pop-small">
                        @lang('Delete')
                    </x-slot:content>
                </x-popover>
            </div>

            @endscope
        </x-table>
    </x-card>
    @endif
</div>