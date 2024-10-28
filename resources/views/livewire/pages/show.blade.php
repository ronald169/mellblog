<?php

use Livewire\Volt\Component;
use App\Models\Page;

new class extends Component {

    public Page $page;

    public function mount(Page $page)
    {
        if (!$page->active) {
            abort(404);
        }

        $this->page = $page;
    }

}; ?>

<div>
    @section('title', $page->seo_title ?? $page->title)
    @section('description', $page->meta_description)
    @section('keywords', $page->meta_keywords)

    <div class="flex justify-end gap-4">
        @auth
            @if (Auth::user()->isAdmin())
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon="c-pencil-square" link="#" spinner
                            class="btn-ghost btn-sm" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Edit this page')
                    </x-slot:content>
                </x-popover>
            @endif
        @endauth
    </div>

    <x-header title="{!! $page->title !!}" />

    <div class="relative items-center w-full px-5 py-5 mx-auto prose md:px-12 max-w-7xl">
        {!! $page->body !!}
    </div>
</div>
