<div>
    <x-form wire:submit='save' separator>
        <x-input label="{{ __('Title') }}" wire:model.debounce.500ms='title' wire:change="$refresh" />
        <x-input label="{{ __('Slug') }}" wire:model='slug' />

        <x-slot:actions>
            <x-button label="{{ __('Save') }}" type='submit' icon='o-paper-airplane' spinner='save'
                class='btn-primary' />
        </x-slot:actions>

    </x-form>
</div>
