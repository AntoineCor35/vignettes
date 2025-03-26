@props(['card', 'detailed' => false])

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
@endphp

<div x-data="{ cardId: {{ $cardId }} }"
    :class="{
        'col-span-1 row-span-1': itemSizes[cardId] === 'small',
        'col-span-1 md:col-span-2 row-span-1': itemSizes[cardId] === 'wide',
        'col-span-1 md:col-span-2 row-span-2': itemSizes[cardId] === 'large'
    }"
    class="rounded-xl p-6 shadow-sm transition-all duration-200 border {{ $bgColor }}">
    <div class="h-full flex flex-col">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full bg-white/90 backdrop-blur-sm">
                    @if ($card->hasMedia('images'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('videos'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('music'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                </div>
                <h3 class="text-lg font-medium">{{ $card->title }}</h3>
            </div>
        </div>

        @if (auth()->check() && auth()->user()->role === 'admin' && auth()->id() !== $card->user_id)
            <div class="mb-3 bg-yellow-50 text-yellow-700 px-3 py-1 rounded text-sm">
                Propriétaire: {{ $card->user->name }}
            </div>
        @endif

        <div class="mb-4 overflow-hidden rounded-md"
            :class="{
                'h-40': itemSizes[cardId] === 'small',
                'h-52': itemSizes[cardId] === 'wide',
                'h-64': itemSizes[cardId] === 'large'
            }">
            @if ($card->hasMedia('images'))
                <img src="{{ $card->getThumbnailUrl() }}" alt="{{ $card->title }}"
                    class="w-full h-full object-cover" />
            @elseif ($card->hasMedia('videos'))
                <div class="relative w-full h-full bg-black flex items-center justify-center">
                    @if ($card->getFirstMedia('videos')->hasGeneratedConversion('thumb'))
                        <img src="{{ $card->getFirstMedia('videos')->getUrl('thumb') }}" alt="Aperçu vidéo"
                            class="max-w-full max-h-full object-contain" />
                    @else
                        <div class="bg-gray-200 w-full h-full flex items-center justify-center">
                            <span class="text-gray-500">Aperçu en cours de génération</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-16 h-16 bg-white bg-opacity-75 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none"
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-400 mx-auto mb-2"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        <span class="text-sm text-gray-700">{{ $card->getFirstMedia('music')->file_name }}</span>
                    </div>
                </div>
            @else
                <div class="w-full h-full bg-white/80 flex items-center justify-center">
                    <span class="text-gray-500">Aucun média</span>
                </div>
            @endif
        </div>

        <p class="text-gray-600 mb-4 line-clamp-2 flex-grow">
            {{ Str::limit($card->description, 150) }}
        </p>

        <div class="flex justify-between items-center mt-auto">
            <span class="text-sm bg-white/80 text-gray-800 px-2 py-1 rounded">
                {{ $card->category ? $card->category->name : 'Non catégorisé' }}
            </span>
            <a href="{{ route('cards.show', $card) }}"
                class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Voir
            </a>
        </div>
    </div>
</div>
