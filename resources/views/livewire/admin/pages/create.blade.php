<?php

use Livewire\Volt\Component;
use App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\{Layout, Title, Validate};
use Mary\Traits\Toast;

new
#[Layout('components.layouts.admin')]
#[Title('Create Page')]
class extends Component {
    use Toast;

    #[Validate('required|max:65000')]
    public string $body = '';

    #[Validate('required|max:255')]
    public string $title = '';

    #[Validate('required|max:255|unique:posts,title,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    public string $slug = '';

    #[Validate('required')]
    public bool $active = false;

    #[Validate('required|max:70')]
    public string $seo_title = '';

    #[Validate('required|max:160')]
    public string $meta_description = '';

    #[Validate('required|regex:/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/')]
    public string $meta_keywords = '';

    public function updatedTitle($value): void
    {
        $this->seo_title = Str::of($value)->slug('-');
        $this->slug = Str::of($value)->slug('-');
    }

    public function save(): void
    {
        $data = $this->validate();

        Page::create($data);

        $this->success(__('Page added with success.'), redirectTo: '/admin/pages/index');
    }


}; ?>

<div>

    <x-header title="{{ __('Create a page') }}" separator progress-indicator>
        <x-slot:actions class='lg:hidden'>
            <x-button label="{{ __('Dashboard') }}" icon='o-building-office-2' class='btn-outline'
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>

    <div class="">
        @include('livewire.admin.pages.page-form')
    </div>

</div>
