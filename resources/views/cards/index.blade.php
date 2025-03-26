<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if (auth()->user()->role === 'admin')
                    {{ __('Toutes les Cartes') }}
                @else
                    {{ __('Mes Cartes') }}
                @endif
            </h2>
            <a href="{{ route('cards.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Créer une carte
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($cards->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p class="text-lg">Vous n'avez pas encore créé de cartes.</p>
                        <a href="{{ route('cards.create') }}"
                            class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Créer ma première carte
                        </a>
                    </div>
                </div>
            @else
                <div x-data="bentoGrid()" class="space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <h2 class="text-xl font-semibold">Mes Cartes</h2>
                        <div class="flex flex-wrap gap-2">
                            <button @click="setAllToSize('small')"
                                class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                Toutes Petites
                            </button>
                            <button @click="setAllToSize('wide')"
                                class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                Toutes Larges
                            </button>
                            <button @click="setAllToSize('large')"
                                class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                Toutes Grandes
                            </button>
                            <button @click="resetToDefaults()"
                                class="px-4 py-2 bg-indigo-500 text-white border border-transparent rounded-md text-sm font-medium hover:bg-indigo-600 transition-colors">
                                Réinitialiser
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($cards as $card)
                            @if (!$card->deleted)
                                @php
                                    // Définir une couleur différente basée sur la catégorie
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
                                @endphp
                                <div x-data="{ cardId: {{ $card->id }} }"
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
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @elseif ($card->hasMedia('videos'))
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    @elseif ($card->hasMedia('music'))
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <h3 class="text-lg font-medium">{{ $card->title }}</h3>
                                            </div>
                                            <select x-model="itemSizes[cardId]"
                                                @change="updateItemSize(cardId, $event.target.value)"
                                                class="w-[110px] h-8 text-sm border rounded bg-white/90 px-2 py-1">
                                                <option value="small">Petit (1×1)</option>
                                                <option value="wide">Large (2×1)</option>
                                                <option value="large">Grand (2×2)</option>
                                            </select>
                                        </div>

                                        @if (auth()->user()->role === 'admin' && auth()->id() !== $card->user_id)
                                            <div class="mb-3 bg-yellow-50 text-yellow-700 px-3 py-1 rounded text-sm">
                                                Propriétaire: {{ $card->user->name }}
                                            </div>
                                        @endif

                                        <div class="mb-4 overflow-hidden rounded-md"
                                            :class="{
                                                'h-40': itemSizes[cardId] === 'small',
                                                'h-52': itemSizes[
                                                    cardId] === 'wide',
                                                'h-64': itemSizes[cardId] === 'large'
                                            }">
                                            @if ($card->hasMedia('images'))
                                                <img src="{{ $card->getFirstMediaUrl('images', 'grid') }}"
                                                    alt="{{ $card->title }}" class="w-full h-full object-cover" />
                                            @elseif ($card->hasMedia('videos'))
                                                <div
                                                    class="relative w-full h-full bg-black flex items-center justify-center">
                                                    @if ($card->getFirstMedia('videos')->hasGeneratedConversion('thumb'))
                                                        <img src="{{ $card->getFirstMedia('videos')->getUrl('thumb') }}"
                                                            alt="Aperçu vidéo"
                                                            class="max-w-full max-h-full object-contain" />
                                                    @else
                                                        <div
                                                            class="bg-gray-200 w-full h-full flex items-center justify-center">
                                                            <span class="text-gray-500">Aperçu en cours de
                                                                génération</span>
                                                        </div>
                                                    @endif
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div
                                                            class="w-16 h-16 bg-white bg-opacity-75 rounded-full flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-8 w-8 text-indigo-600" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($card->hasMedia('music'))
                                                <div
                                                    class="w-full h-full bg-white/80 flex items-center justify-center">
                                                    <div class="text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-16 w-16 text-indigo-400 mx-auto mb-2"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                        </svg>
                                                        <span
                                                            class="text-sm text-gray-700">{{ $card->getFirstMedia('music')->file_name }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div
                                                    class="w-full h-full bg-white/80 flex items-center justify-center">
                                                    <span class="text-gray-500">Aucun média</span>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-gray-600 mb-4 line-clamp-2 flex-grow">
                                            {{ Str::limit($card->description, 150) }}
                                        </p>

                                        <div class="flex justify-between items-center mt-auto">
                                            <span
                                                class="text-sm bg-white/80 text-gray-800 px-2 py-1 rounded">{{ $card->category->name }}</span>
                                            <a href="{{ route('cards.show', $card) }}"
                                                class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js Script -->
    <script>
        function bentoGrid() {
            return {
                itemSizes: {},

                init() {
                    // Initialiser les tailles par défaut - assigner des tailles variées en fonction de l'ID
                    @foreach ($cards as $card)
                        @if (!$card->deleted)
                            // Attribution de tailles différentes par défaut basées sur l'ID
                            @if ($loop->iteration % 3 == 0)
                                this.itemSizes[{{ $card->id }}] = 'large';
                            @elseif ($loop->iteration % 2 == 0)
                                this.itemSizes[{{ $card->id }}] = 'wide';
                            @else
                                this.itemSizes[{{ $card->id }}] = 'small';
                            @endif
                        @endif
                    @endforeach
                },

                updateItemSize(id, size) {
                    this.itemSizes[id] = size;
                },

                setAllToSize(size) {
                    @foreach ($cards as $card)
                        @if (!$card->deleted)
                            this.itemSizes[{{ $card->id }}] = size;
                        @endif
                    @endforeach
                },

                resetToDefaults() {
                    @foreach ($cards as $card)
                        @if (!$card->deleted)
                            // Réinitialiser à la taille par défaut variée
                            @if ($loop->iteration % 3 == 0)
                                this.itemSizes[{{ $card->id }}] = 'large';
                            @elseif ($loop->iteration % 2 == 0)
                                this.itemSizes[{{ $card->id }}] = 'wide';
                            @else
                                this.itemSizes[{{ $card->id }}] = 'small';
                            @endif
                        @endif
                    @endforeach
                }
            }
        }
    </script>
</x-app-layout>
