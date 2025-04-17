@props(['card', 'detailed' => false, 'showActions' => false, 'size' => 'medium'])

@php
    $categoryColors = [
        'bg-pink-500/10 hover:bg-pink-500/20',
        'bg-blue-500/10 hover:bg-blue-500/20',
        'bg-purple-500/10 hover:bg-purple-500/20',
        'bg-green-500/10 hover:bg-green-500/20',
        'bg-yellow-500/10 hover:bg-yellow-500/20',
        'bg-red-500/10 hover:bg-red-500/20',
        'bg-orange-500/10 hover:bg-orange-500/20',
        'bg-teal-500/10 hover:bg-teal-500/20',
        'bg-indigo-500/10 hover:bg-indigo-500/20',
    ];
    $colorIndex = $card->category_id % count($categoryColors);
    $bgColor = $categoryColors[$colorIndex];
    $cardId = $card->id;
    $modalId = "card-modal-{$cardId}";

    // Classes de ratio d'aspect selon la taille
$aspectRatioClass = match ($size) {
    'small' => 'aspect-square', // Petit (1x1)
    'wide' => 'aspect-[2/1]', // Large (2x1)
    'large' => 'aspect-square col-span-2 row-span-2', // Grand (2x2)
    default => 'aspect-[3/4]', // Ratio standard pour les autres
    };
@endphp

<div x-data="{ showInfo: false }"
    class="group relative overflow-hidden rounded-lg cursor-pointer w-full {{ $aspectRatioClass }}"
    @click="$dispatch('open-modal', '{{ $modalId }}')" @mouseenter="showInfo = true" @mouseleave="showInfo = false">

    <!-- Image ou média principal -->
    <div class="w-full h-full">
        @if ($card->hasMedia('images'))
            <img src="{{ $card->getThumbnailUrl() }}" alt="{{ $card->title }}"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
        @elseif ($card->hasMedia('videos'))
            <div class="relative w-full h-full bg-black flex items-center justify-center overflow-hidden">
                <video src="{{ $card->getFirstMediaUrl('videos') }}" class="w-full h-full object-cover" muted loop
                    preload="metadata" x-ref="video" x-on:mouseenter="$refs.video.play()"
                    x-on:mouseleave="$refs.video.pause()" @click.stop></video>
                <div class="absolute inset-0 flex items-center justify-center" x-show="!showInfo">
                    <div class="w-12 h-12 bg-white/75 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        @elseif ($card->hasMedia('music'))
            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-400 mx-auto mb-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    <span
                        class="text-xs text-gray-700 truncate block max-w-full">{{ $card->getFirstMedia('music')->file_name }}</span>
                </div>
            </div>
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                <span class="text-gray-500 text-sm">Aucun média</span>
            </div>
        @endif
    </div>

    <!-- Overlay avec informations au survol -->
    <div x-show="showInfo" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent p-4 flex flex-col justify-end">
        <h3 class="text-white font-medium text-lg mb-1 truncate">{{ $card->title }}</h3>
        <p class="text-white/80 text-sm line-clamp-2 mb-2">{{ Str::limit($card->description, 80) }}</p>
        <div class="flex items-center justify-between">
            <span class="text-white/90 text-xs bg-white/20 px-2 py-1 rounded-full">
                {{ $card->category ? $card->category->name : 'Non catégorisé' }}
            </span>
            @if ($showActions && auth()->check() && (auth()->id() === $card->user_id || auth()->user()->role === 'admin'))
                <div class="flex items-center gap-1">
                    <a href="{{ route('cards.edit', $card) }}"
                        class="text-white/90 hover:text-white bg-white/20 p-1.5 rounded-full" @click.stop>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Utilisation du composant modal séparé -->
<x-card-modal :card="$card" :modalId="$modalId" :bgColor="$bgColor" :showActions="$showActions" />
