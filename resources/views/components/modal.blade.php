@props(['id', 'maxWidth'])

@php
    $id = $id ?? md5($attributes->wire('model'));
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
        'full' => 'sm:max-w-full',
    ][$maxWidth ?? '2xl'];
@endphp

<div x-data="{ show: false }"
    x-on:open-modal.window="if($event.detail == '{{ $id }}') { show = true; $dispatch('open-modal', '{{ $id }}'); }"
    x-on:close-modal.window="if($event.detail == '{{ $id }}') { show = false; $dispatch('close-modal', '{{ $id }}'); }"
    x-on:keydown.escape.window="show = false; $dispatch('close-modal', '{{ $id }}');" x-show="show"
    id="{{ $id }}" class="fixed inset-0 z-50 px-4 py-6 overflow-y-auto flex items-center justify-center"
    style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all"
        x-on:click="show = false; $dispatch('close-modal', '{{ $id }}');">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show"
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }}"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        @click.outside="show = false; $dispatch('close-modal', '{{ $id }}');">
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
