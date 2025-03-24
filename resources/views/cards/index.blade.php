<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Cartes') }}
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($cards as $card)
                        @if (!$card->deleted)
                            <div
                                class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition duration-200">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-semibold">{{ $card->title }}</h3>
                                        <span
                                            class="text-sm text-gray-500">{{ $card->creation_date->format('d/m/Y') }}</span>
                                    </div>

                                    {{-- Affichage de l'image principale s'il y en a une --}}
                                    @if ($card->hasMedia('images'))
                                        <div class="mb-4 overflow-hidden rounded-md" style="height: 200px;">
                                            <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
                                                class="w-full h-full object-cover" />
                                        </div>
                                    @endif

                                    {{-- Affichage du début de la description --}}
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($card->description, 150) }}
                                    </p>

                                    {{-- Icônes pour les différents types de média --}}
                                    <div class="flex space-x-2 mb-4">
                                        @if ($card->hasMedia('images'))
                                            <span class="inline-flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Image
                                            </span>
                                        @endif

                                        @if ($card->hasMedia('videos'))
                                            <span class="inline-flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                Vidéo
                                            </span>
                                        @endif

                                        @if ($card->hasMedia('music'))
                                            <span class="inline-flex items-center text-sm text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                </svg>
                                                Musique
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span
                                            class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ $card->category->name }}</span>
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
            @endif
        </div>
    </div>
</x-app-layout>
