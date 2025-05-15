<?php

use Livewire\Volt\Component;
use App\Models\Page;
use Livewire\Attributes\{Layout, Title};
use Livewire\WithPagination;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.admin')]
#[Title('Pages')]
class extends Component {

    use Toast, WithPagination;

    public function headers(): array
    {
        return [
            ['key' => 'title', 'label' => __('Title')],
            ['key' => 'slug', 'label' => __('Slug')],
            ['key' => 'active', 'label' => __('Published')],
        ];
    }

    public function deletePage(Page $page)
    {
        $page->delete();
        $this->success(__('Page deleted'));
    }

    public function with(): array
    {
        return [
            'pages' => Page::query()
                ->select('id', 'title', 'slug', 'active')
                ->orderBy('title', 'desc')
                ->paginate(10),
            'headers' => $this->headers(),
        ];
    }

}; ?>

<div>
    <x-header title="{{ __('Pages') }}" separator progress-indicator>
        <x-slot:actions class="lg:hidden">
            <x-button link="{{ route('admin') }}" icon="s-building-office-2" label="{{ __('Dashboard') }}"
                class='btn-outline' />
            <x-button link="#" icon="s-document-plus" label="{{ __('Add a page') }}" class='btn-outline' />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table striped :headers="$headers" :rows="$pages" link='/admin/pages/{slug}/edit'>
            @scope('cell_active', $page)
            @if ($page->active)
            <x-icon name="o-check-circle" class="text-green-500" />
            @endif
            @endscope

            @scope('actions', $page)
            <x-popover>
                <x-slot:trigger>
                    <x-button icon='s-trash' wire:click="deletePage({{ $page->id }})"
                        wire:confirm="{{ __('Are you sure to delete this page?') }}"
                        class='text-red-500 btn-ghost btn-sm' />
                </x-slot:trigger>
                <x-slot:content class="pop-small">
                    @lang('Delete')
                </x-slot:content>
            </x-popover>
            @endscope
        </x-table>
    </x-card>
</div>
