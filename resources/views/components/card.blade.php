@props(['card', 'detailed' => false, 'showActions' => false])

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

    // Déterminer la taille de la carte
    $cardSizeName = $card->cardSize->name ?? 'Moyen';
    $isSmallCard = $cardSizeName === 'Petit';
    $isMediumCard = $cardSizeName === 'Moyen';
    $isLargeCard = $cardSizeName === 'Grand';

    // Définir les hauteurs pour que medium et small soient 50% de la hauteur des grandes cartes
    // Large: 320px, Medium et Small: 160px (50% de large)
    $mediaHeightClass = $isLargeCard ? 'h-[320px]' : 'h-[160px]';
@endphp

<div x-data="{ cardId: {{ $cardId }} }"
    :class="{
        'col-span-1 row-span-1': itemSizes[cardId] === 'small',
        'col-span-1 md:col-span-2 row-span-1': itemSizes[cardId] === 'wide',
        'col-span-1 md:col-span-2 row-span-2': itemSizes[cardId] === 'large'
    }"
    class="rounded-xl p-3 md:p-4 shadow-sm transition-all duration-200 border {{ $bgColor }} cursor-pointer h-full w-full flex flex-col"
    style="display: flex; flex-direction: column;" @click="$dispatch('open-modal', '{{ $modalId }}')">
    <div class="flex-1 flex flex-col">
        <div class="flex justify-between items-start mb-2">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-full bg-white/90 backdrop-blur-sm">
                    @if ($card->hasMedia('images'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('videos'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('music'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                </div>
                <h3 class="text-sm md:text-base font-medium truncate">{{ $card->title }}</h3>
            </div>
        </div>

        @if (auth()->check() && auth()->user()->role === 'admin' && auth()->id() !== $card->user_id)
            <div class="mb-2 bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded text-xs">
                Propriétaire: {{ $card->user->name }}
            </div>
        @endif

        <div class="mb-2 overflow-hidden rounded-md flex-grow {{ $mediaHeightClass }}">
            @if ($card->hasMedia('images'))
                <img src="{{ $card->getThumbnailUrl() }}" alt="{{ $card->title }}"
                    class="w-full h-full object-cover" />
            @elseif ($card->hasMedia('videos'))
                <div class="relative w-full h-full bg-black flex items-center justify-center overflow-hidden"
                    x-data="{ isHovering: false }">
                    <video src="{{ $card->getFirstMediaUrl('videos') }}" class="w-full h-full object-cover" muted loop
                        preload="metadata" x-ref="video" x-on:mouseenter="isHovering = true; $refs.video.play()"
                        x-on:mouseleave="isHovering = false; $refs.video.pause()" @click.stop></video>
                    <div class="absolute inset-0 flex items-center justify-center" x-show="!isHovering">
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
                <div class="w-full h-full bg-white/80 flex items-center justify-center">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-400 mx-auto mb-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        <span
                            class="text-xs text-gray-700 truncate block max-w-full">{{ $card->getFirstMedia('music')->file_name }}</span>
                    </div>
                </div>
            @else
                <div class="w-full h-full bg-white/80 flex items-center justify-center">
                    <span class="text-gray-500 text-sm">Aucun média</span>
                </div>
            @endif
        </div>

        <p class="text-gray-600 mb-2 line-clamp-2 text-xs md:text-sm">
            {{ Str::limit($card->description, 100) }}
        </p>

        <div class="flex justify-between items-center mt-auto">
            <span class="text-xs bg-white/80 text-gray-800 px-2 py-1 rounded truncate max-w-[40%]">
                {{ $card->category ? $card->category->name : 'Non catégorisé' }}
            </span>
            <div class="flex items-center gap-1">
                @if ($showActions && auth()->check() && (auth()->id() === $card->user_id || auth()->user()->role === 'admin'))
                    <a href="{{ route('cards.edit', $card) }}"
                        class="inline-flex items-center px-1.5 py-0.5 bg-blue-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                        @click.stop>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span class="hidden md:inline">Éditer</span>
                    </a>
                    <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-1.5 py-0.5 bg-red-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-red-700"
                            @click.stop onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte ?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            <span class="hidden md:inline">Suppr</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('cards.show', $card) }}"
                    class="inline-flex items-center px-1.5 py-0.5 bg-indigo-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    @click.stop>
                    Voir
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour cette carte -->
<x-modal :id="$modalId" maxWidth="4xl">
    <div class="flex flex-col">
        <!-- Header de la modal -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full {{ $bgColor }}">
                    @if ($card->hasMedia('images'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('videos'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('music'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                </div>
                <h2 class="text-2xl font-bold">{{ $card->title }}</h2>
            </div>
            <button @click="$dispatch('close-modal', '{{ $modalId }}')"
                class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Contenu de la modal -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Media de la carte -->
            <div class="w-full md:w-2/3 overflow-hidden rounded-lg">
                @if ($card->hasMedia('images'))
                    <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
                        class="w-full h-auto object-cover" />
                @elseif ($card->hasMedia('videos'))
                    <div class="relative w-full aspect-video bg-black flex items-center justify-center"
                        x-data="{
                            videoReady: false,
                            setupVideo() {
                                const video = this.$refs.modalVideo;
                                video.addEventListener('canplay', () => {
                                    this.videoReady = true;
                                    if ($el.closest('[x-data]').show) {
                                        video.play();
                                    }
                                });
                            }
                        }" x-init="setupVideo()"
                        @open-modal.window="if ($event.detail == '{{ $modalId }}' && videoReady) { $nextTick(() => $refs.modalVideo.play()); }"
                        @close-modal.window="if ($event.detail == '{{ $modalId }}') { $refs.modalVideo.pause(); }">
                        <video src="{{ $card->getFirstMediaUrl('videos') }}" class="w-full h-auto" controls
                            preload="auto" x-ref="modalVideo"></video>
                    </div>
                @elseif ($card->hasMedia('music'))
                    <div class="w-full p-8 bg-white/80 flex items-center justify-center rounded-lg border">
                        <div class="text-center w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-indigo-400 mx-auto mb-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            <div class="text-lg text-gray-700 mb-4">{{ $card->getFirstMedia('music')->file_name }}
                            </div>
                            <audio controls class="w-full">
                                <source src="{{ $card->getFirstMediaUrl('music') }}"
                                    type="{{ $card->getFirstMedia('music')->mime_type }}">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                        </div>
                    </div>
                @else
                    <div class="w-full h-64 bg-white/80 flex items-center justify-center rounded-lg border">
                        <span class="text-gray-500 text-lg">Aucun média disponible</span>
                    </div>
                @endif
            </div>

            <!-- Informations de la carte -->
            <div class="w-full md:w-1/3 flex flex-col">
                @if (auth()->check() && auth()->user()->role === 'admin' && auth()->id() !== $card->user_id)
                    <div class="mb-4 bg-yellow-50 text-yellow-700 px-4 py-2 rounded-md">
                        <div class="font-medium">Propriétaire:</div>
                        <div>{{ $card->user->name }}</div>
                        <div class="text-sm">{{ $card->user->email }}</div>
                    </div>
                @endif

                <div class="mb-4">
                    <h3 class="text-lg font-medium border-b pb-2 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $card->description }}</p>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-medium border-b pb-2 mb-2">Détails</h3>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                        <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('home', ['category' => $card->category_id]) }}"
                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                @click="$dispatch('close-modal', '{{ $modalId }}')">
                                {{ $card->category ? $card->category->name : 'Non catégorisé' }}
                            </a>
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Auteur</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('home', ['author' => $card->user_id]) }}"
                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                @click="$dispatch('close-modal', '{{ $modalId }}')">
                                {{ $card->user->name }}
                            </a>
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Taille</dt>
                        <dd class="text-sm text-gray-900">{{ $card->cardSize ? $card->cardSize->name : 'Standard' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="text-sm text-gray-900">{{ $card->creation_date->format('d/m/Y') }}</dd>
                    </dl>
                </div>

                <div class="mt-auto pt-4 border-t">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('cards.show', $card) }}"
                            class="flex-1 flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none">
                            Voir les détails complets
                        </a>

                        @if ($showActions && auth()->check() && (auth()->id() === $card->user_id || auth()->user()->role === 'admin'))
                            <a href="{{ route('cards.edit', $card) }}"
                                class="flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none"
                                @click.stop>
                                Éditer
                            </a>

                            <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte ?')">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
