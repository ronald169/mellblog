<x-form wire:submit='save'>
    <x-input label="{{ __('Title') }}" wire:model.live='title' wire:change="$refresh"
        placeholder="{{ __('Enter the title') }}" />
    <x-input label="{{ __('Slug') }}" wire:model='slug' />

    <x-checkbox label="{{ __('Published') }}" wire:model='active' />

    <x-editor label="{{ __('Content') }}" wire:model='body' :config="config('tinymce.config')"
        folder="{{ 'photos/' . now()->format('Y/m') }}" />

    <x-card title="{{ __('SEO') }}">
        <x-input label="{{ __('Meta Title') }}" wire:model='seo_title' hint="{{ __('Max 70 chars') }}" />
        <x-textarea label="{{ __('Meta Description') }}" wire:model='meta_description' hint="{{ __('Max 160 chars') }}"
            rows='2' inline />
        <x-textarea label="{{ __('Meta Keywords') }}" wire:model='meta_keywords' rows='1' inline
            hint="{{ __('Keywords separated by comma') }}" />

    </x-card>

    <x-slot:actions>
        <x-button type="submit" class='btn-primary' label="{{ __('Save') }}" icon='o-paper-airplane' spinner='save' />
    </x-slot:actions>

</x-form>
