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
                        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 xl:columns-5 gap-4">
                            @foreach ($cards as $card)
                                <div class="break-inside-avoid mb-4">
                                    <x-card :card="$card" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
