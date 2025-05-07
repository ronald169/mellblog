<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Title, Layout, Validate};
use App\Models\{Category, Post};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new
#[Title('Create Post')]
#[Layout('components.layouts.admin')]
class extends Component {

    use Toast, WithFileUploads;

    public int $category_id;

    #[Validate('required|image|max:2000')]
    public ?TemporaryUploadedFile $photo = null;

    #[Validate('required|string|max:16777215')]
    public string $body = '';

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|max:255|unique:posts,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    public string $slug = '';

    #[Validate('required')]
    public bool $active = false;

    #[Validate('required')]
    public bool $pinned = false;

    #[Validate('required|max:70')]
    public string $seo_title = '';

    #[Validate('required|max:160')]
    public string $meta_description = '';

    #[Validate('required|regex:/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/')]
    public string $meta_keywords = '';

    public function mount(): void
    {
        $category = Category::first();
        $this->category_id = $category->id;
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
        $this->seo_title = $value;
    }

    public function save()
    {
        $data = $this->validate();

        $date = now()->format('Y/m');
        $path = $date . '/' . basename($this->photo->store('photos/' . $date, 'public'));

        $data['body'] = replaceAbsoluteUrlsWithRelative($data['body']);

        Post::create(
            $data + [
                'user_id' => Auth::id(),
                'category_id' => $this->category_id,
                'image' => $path,
            ]
        );

        $this->success(__('Post added with success.'), redirectTo: '/admin/posts/index');
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('title', 'desc')->get(),
        ];
    }

}; ?>

<div>

    <x-header title="{{ __('Add a post') }}" separator progress-indication>
        <x-slot:actions>
            <x-button icon='s-building-office-2' label="{{ __('Dashboard') }}" link="{{ route('admin') }}"
                class="lg:hidden btn-outline" />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-4">
        <x-card>
            <x-form wire:submit="save">
                <x-select label="{{ __('Category') }}" :options="$categories" option-label="title"
                    wire:model="category_id" wire:change="$refresh" />

                <br>

                <div class="flex gap-6">
                    <x-checkbox label="{{ __('Published') }}" wire:model="active" />
                    <x-checkbox label="{{ __('Pinned') }}" wire:model="pinned" />
                </div>
                <x-input type="text" wire:model.live="title" label="{{ __('Title') }}"
                    placeholder="{{ __('Enter the title') }}" wire:change="$refresh" />

                <x-input type='text' wire:model="slug" label="{{ __('Slug') }}" />

                <x-editor wire:model="body" label="{{ __('Content') }}" :config="config('tinymce.config')"
                    folder="{{ 'photos/' . now()->format('Y/m') }}" />

                <x-card title="{{ __('SEO') }}" shadow separator>
                    <x-input placeholder="{{ __('Title') }}" wire:model='seo_title' hint="{{ __('Max 70 chars') }}" />

                    <x-textarea placeholder="{{ __('Meta description') }}" wire:model='meta_description'
                        hint="{{ __('Max 160 chars') }}" />

                    <br>
                    <x-textarea placeholder="{{ __('Meta keywords') }}" wire:model="meta_keywords" hint="{{ __('Keywords
                        separate by coma') }}" rows='1' inline />

                    <x-file wire:model='photo' label="{{__('Featured image') }}"
                        hint="{{ __('Click on the image to modify') }}" accept="image/png, image/jpeg">
                        <img src="{{ $photo == '' ? '/storage/ask.jpg' : $photo }}" class='h-40' />
                    </x-file>

                    <x-slot:actions>
                        <x-button label="{{ __('Save') }}" icon='o-paper-airplane' type='submit' spinner="save"
                            class='btn-primary' />
                    </x-slot:actions>
                </x-card>
            </x-form>
        </x-card>
    </div>

</div>
