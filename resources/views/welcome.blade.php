<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accueil') }}
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="max-w-[98%] mx-auto px-2">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    @if (isset($activeCategory) || isset($activeAuthor))
                        <div class="mb-6 bg-indigo-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-indigo-800 mb-1">Filtres actifs</h3>
                                    <div class="text-sm text-indigo-600">
                                        @if (isset($activeCategory))
                                            Catégorie : <span class="font-medium">{{ $activeCategory->name }}</span>
                                        @endif

                                        @if (isset($activeAuthor))
                                            @if (isset($activeCategory))
                                                |
                                            @endif
                                            Auteur : <span class="font-medium">{{ $activeAuthor->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('home') }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Effacer les filtres
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-between items-center mb-6">
                        <div class="flex flex-wrap gap-2 mb-4 md:mb-0">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center gap-1 px-3 py-2 text-sm border rounded-md bg-white hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Catégories
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                                    style="display: none;">
                                    <div class="py-1">
                                        @foreach ($categories as $category)
                                            <a href="{{ route('home', ['category' => $category->id]) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ $category->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button id="shuffle-btn"
                            class="flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 3 21 3 21 8"></polyline>
                                <line x1="4" y1="20" x2="21" y2="3"></line>
                                <polyline points="21 16 21 21 16 21"></polyline>
                                <line x1="15" y1="15" x2="21" y2="21"></line>
                                <line x1="4" y1="4" x2="9" y2="9"></line>
                            </svg>
                            Mélanger
                        </button>
                    </div>

                    @if ($cards->isEmpty())
                        <div class="py-12 text-center empty-message">
                            <h3 class="text-lg font-medium text-gray-900">Aucune carte trouvée</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if (isset($activeCategory) || isset($activeAuthor))
                                    Aucune carte ne correspond aux filtres sélectionnés.
                                    <a href="{{ route('home') }}" class="text-indigo-600 hover:underline">Effacer
                                        les filtres</a>
                                @else
                                    Commencez par créer une nouvelle carte.
                                @endif
                            </p>
                            @auth
                                <div class="mt-6">
                                    <a href="{{ route('cards.create') }}"
                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                        Créer ma première carte
                                    </a>
                                </div>
                            @else
                                <div class="mt-6">
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                        Se connecter pour créer des cartes
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @else
                        @php
                            // Grouper les cartes par leur taille
                            $smallCards = $cards
                                ->filter(function ($card) {
                                    return $card->cardSize->name === 'Petit';
                                })
                                ->values();

                            $mediumCards = $cards
                                ->filter(function ($card) {
                                    return $card->cardSize->name === 'Moyen';
                                })
                                ->values();

                            $largeCards = $cards
                                ->filter(function ($card) {
                                    return $card->cardSize->name === 'Grand';
                                })
                                ->values();

                            // Utiliser des indices pour suivre quelle carte on affiche
                            $smallIndex = 0;
                            $mediumIndex = 0;
                            $largeIndex = 0;

                            // Fonction pour récupérer la carte en fonction de la taille et incrémenter l'index
                            function getCard($collection, &$index)
                            {
                                if ($collection->isEmpty() || !isset($collection[$index])) {
                                    return null;
                                }
                                $card = $collection[$index];
                                $index = ($index + 1) % $collection->count();
                                return $card;
                            }
                        @endphp

                        <div class="w-full grid grid-cols-5 gap-4 max-w-full auto-rows-auto"
                            style="grid-template-rows: repeat(5, minmax(180px, auto)); grid-auto-flow: dense;">
                            {{-- Position 1: 1x2 (large) --}}
                            <div class="row-span-2 h-full">
                                @if ($largeCard = getCard($largeCards, $largeIndex))
                                    <x-card :card="$largeCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte large disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 13: 2x1 (medium) --}}
                            <div class="col-span-2 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 4-5: 2x1 (medium) --}}
                            <div class="col-span-2 col-start-4 row-start-1 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 14: 1x1 (small) --}}
                            <div class="col-start-2 row-start-2 h-full">
                                @if ($smallCard = getCard($smallCards, $smallIndex))
                                    <x-card :card="$smallCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte petite disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 15: 1x1 (small) --}}
                            <div class="col-start-3 row-start-2 h-full">
                                @if ($smallCard = getCard($smallCards, $smallIndex))
                                    <x-card :card="$smallCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte petite disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 4-5 (row 2): 2x1 (medium) --}}
                            <div class="col-span-2 col-start-4 row-start-2 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 17: 2x1 (medium) --}}
                            <div class="col-span-2 col-start-1 row-start-3 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 16: 1x2 (large) --}}
                            <div class="row-span-2 col-start-3 row-start-3 h-full">
                                @if ($largeCard = getCard($largeCards, $largeIndex))
                                    <x-card :card="$largeCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte large disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 4-5 (row 3): 2x2 (large) --}}
                            <div class="col-span-2 row-span-2 col-start-4 row-start-3 h-full">
                                @if ($largeCard = getCard($largeCards, $largeIndex))
                                    <x-card :card="$largeCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte large disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 18: 1x1 (small) --}}
                            <div class="row-start-4 col-start-1 h-full">
                                @if ($smallCard = getCard($smallCards, $smallIndex))
                                    <x-card :card="$smallCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte petite disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 19: 1x1 (small) --}}
                            <div class="row-start-4 col-start-2 h-full">
                                @if ($smallCard = getCard($smallCards, $smallIndex))
                                    <x-card :card="$smallCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte petite disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 1-3 (row 5): 3x1 (custom) --}}
                            <div class="col-span-3 row-start-5 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>

                            {{-- Position 4-5 (row 5): 2x1 (medium) --}}
                            <div class="col-span-2 col-start-4 row-start-5 h-full">
                                @if ($mediumCard = getCard($mediumCards, $mediumIndex))
                                    <x-card :card="$mediumCard" />
                                @else
                                    <div
                                        class="bg-gray-100 rounded-lg h-full flex items-center justify-center text-gray-400">
                                        Pas de carte moyenne disponible
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('shuffle-btn')?.addEventListener('click', function() {
                window.location.reload();
            });
        });
    </script>

    @if (!$cards->isEmpty())
        <script>
            function bentoGrid() {
                return {
                    itemSizes: {},
                    init() {
                        @foreach ($cards as $card)
                            @if ($card->cardSize->name === 'Petit')
                                this.itemSizes[{{ $card->id }}] = 'small';
                            @elseif ($card->cardSize->name === 'Moyen')
                                this.itemSizes[{{ $card->id }}] = 'wide';
                            @else
                                this.itemSizes[{{ $card->id }}] = 'large';
                            @endif
                        @endforeach
                    }
                }
            }
        </script>
    @endif
</x-app-layout>
