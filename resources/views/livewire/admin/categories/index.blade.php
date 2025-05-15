<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Validate, Title};
use Mary\Traits\Toast;
use Illuminate\Support\Str;


new
#[Layout('components.layouts.admin')]
#[Title('Category')]
class extends Component {

    use WithPagination, Toast;

    #[Validate('required|string|max:255|unique:categories,title')]
    public string $title = '';

    #[Validate('required|max:255|unique:categories,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    public string $slug = '';

    public function updatedTitle($value): void
    {
        $this->generateSlug($value);
    }

    public function generateSlug(string $title):void
    {
        $this->slug = Str::of($title)->slug('-');
    }

    public function save(): void
    {
        $data = $this->validate();

        Category::create($data);

        $this->title = $this->slug = '';

        $this->success(__('Category created with success.'));
    }

    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];

    public function headers(): array
    {
        return [
            ['key' => 'title', 'label' => __('Title'), 'sortable' => true],
            ['key' => 'slug', 'label' => __('Slug')],
            ['key' => 'posts_count', 'label' => __('Posts count')],
        ];
    }

    public function categories()
    {
        return Category::query()
            ->withCount('posts')
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();

        $this->success(__('Category deleted with success.'));
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'categories' => $this->categories(),
        ];
    }

}; ?>

<div class='grid gap-5'>
    <x-header title="{{ __('Categories') }}" separator progress-indicator>
        <x-slot:actions class='lg:hidden'>
            <x-button icon='s-building-office-2' link="{{ route('admin') }}" class='btn-ghost' />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :rows="$categories" :headers="$headers" :sort-by="$sortBy" striped with-pagination
            link="/admin/categories/{id}/edit">
            @scope('actions', $category)
            <x-popover>
                <x-slot:trigger>
                    <x-button icon='o-trash' wire:click="deleteCategory({{ $category->id }})"
                        wire:confirm="{{ __('Are you sure to delete this category?') }}" spinner
                        class="text-red-500 btn-ghost btn-sm" />
                </x-slot:trigger>
                <x-slot:content class='pop-small'>
                    @lang('Delete')
                </x-slot:content>
            </x-popover>
            @endscope
        </x-table>
    </x-card>

    <x-card title="{{ __('Create a new category') }}">
        @include('livewire.admin.categories.category-form')
    </x-card>
</div>
