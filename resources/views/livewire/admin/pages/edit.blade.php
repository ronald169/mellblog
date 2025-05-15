<?php

use Livewire\Volt\Component;
use App\Models\Page;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Attributes\{Layout, Title, Validate};
use Mary\Traits\Toast;

new
#[Layout('components.layouts.admin')]
#[Title('Edit page')]
class extends Component {
    use Toast;

    public Page $page;
	public string $body             = '';
	public string $title            = '';
	public string $slug             = '';
	public bool $active             = false;
	public string $seo_title        = '';
	public string $meta_description = '';
	public string $meta_keywords    = '';

    public function mount(): void
    {
        $this->fill($this->page);
    }

    public function updatedTitle($value): void
    {
        $this->title_seo = $this->slug = Str::of($value)->slug('-');
    }

    public function save(): void
    {
        $data = $this->validate([
            'title'            => 'required|string|max:255',
			'body'             => 'required|max:65000',
			'active'           => 'required',
			'slug'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('pages')->ignore($this->page->id)],
			'seo_title'        => 'required|max:70',
			'meta_description' => 'required|max:160',
			'meta_keywords'    => 'required|regex:/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/',
        ]);

        $this->page->update($data);

        $this->success(__('Page edited with success.'), redirectTo: '/admin/pages/index');
    }

}; ?>

<div>
    <x-header title="{{ __('Edit ') . $page->title }}" separator progress-indicator>
        <x-slot:actions class='lg:hidden'>
            <x-button label="{{ __('Dashboard') }}" icon='s-building-office-2' class='btn-outline'
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>

    @include('livewire.admin.pages.page-form')
</div>
