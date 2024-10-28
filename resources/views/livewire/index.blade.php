<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\WithPagination;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use WithPagination;

    public ?Category $category = null;
    public string $param = '';

    public function mount(string $slug = '', string $param = ''): void
    {
        $this->param = $param;

        if (request()->is('category/*')) {
            $this->category = $this->getCategoryBySlug($slug);
        }
    }

    public function getPosts(): LengthAwarePaginator
    {
        $postRepository = new PostRepository();

        if (!empty($this->param)) {
            return $postRepository->search($this->param);
        }

        return $postRepository->getPostsPaginate($this->category);
    }

    protected function getCategoryBySlug(string $slug): ?Category
    {
        return 'category' === request()->segment(1) ? Category::whereSlug($slug)->firstOrFail() : null;
    }

    public function with(): array
    {
        return [
            'posts' => $this->getPosts(),
        ];
    }

}; ?>

<div>
    <div class="relative grid items-center w-full py-5 mx-auto md:px-12 max-w-7xl">

        @if ($category)
        <x-header title="{{ __('Posts for category ') }} {{ $category->title }}" size="text-2xl sm:text-3xl md:text-4xl" />
        @elseif($param !== '')
            <x-header title="{{ __('Posts for search ') }} '{{ $param }}'" size="text-2xl sm:text-3xl md:text-4xl" />
        @endif

        <div class="mb-4 mary-table-pagination">
            <div class="mb-5 border border-t-0 border-x-0 border-b-1 border-b-base-300"></div>
            {{ $posts->links() }}
        </div>

        <div class="container mx-auto">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($posts as $post)
                    <x-card
                        class="w-full transition duration-500 ease-in-out shadow-md shadow-gray-500 hover:shadow-xl hover:shadow-gray-500"
                        title="{!! $post->title !!}">

                        <div class="text-justify">{!! str(strip_tags($post->excerpt))->words(config('app.excerptSize')) !!}</div>
                        <br>
                        <hr>
                        <div class="flex justify-between">
                            <p wire:click="" class="text-left cursor-pointer">{{ $post->user->name }}</p>
                            <p class="text-right"><em>{{ $post->created_at->isoFormat('LL') }}</em></p>
                        </div>
                        @if($post->image)
                            <x-slot:figure>
                                <a href="{{ url('/posts/' . $post->slug) }}">
                                    <img src="{{ asset('storage/photos/' . $post->image) }}" alt="{{ $post->title }}" />
                                </a>
                            </x-slot:figure>
                        @endif

                        <x-slot:menu>
                            @if ($post->pinned)
                                <x-badge value="{{ __('Pinned') }}" class="p-3 badge-warning" />
                            @endif
                        </x-slot:menu>

                        <x-slot:actions>
                            <div class="flex flex-col items-end space-y-2 sm:items-start sm:flex-row sm:space-y-0 sm:space-x-2">
                                <x-popover>
                                    <x-slot:trigger>
                                        <x-button label="{{ $post->category->title }}"
                                            link="{{ url('/category/' . $post->category->slug) }}" class="mt-1 btn-outline btn-sm" />
                                    </x-slot:trigger>
                                    <x-slot:content class="pop-small">
                                        @lang('Show this category')
                                    </x-slot:content>
                                </x-popover>

                                <x-popover>
                                    <x-slot:trigger>
                                        <x-button label="{{ __('Read') }}" link="{{ url('/posts/' . $post->slug) }}"
                                            class="mt-1 btn-outline btn-sm" />
                                    </x-slot:trigger>
                                    <x-slot:content class="pop-small">
                                        @lang('Read this post')
                                    </x-slot:content>
                                </x-popover>
                            </div>
                        </x-slot:actions>
                    </x-card>
                @empty
                    <div class="col-span-3">
                        <x-card title="{{ __('Nothing to show !') }}">
                            {{ __('No Post found with these criteria') }}
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>


        <!-- Pagination inférieure -->
        <div class="mb-4 mary-table-pagination">
            <div class="mb-5 border border-t-0 border-x-0 border-b-1 border-b-base-300"></div>
            {{ $posts->links() }}
        </div>

    </div>
</div>
