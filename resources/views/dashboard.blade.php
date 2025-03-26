@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Section d'administration visible uniquement pour les administrateurs --}}
            @if (Auth::user()->role === 'admin')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 w-full">
                            <h3 class="text-lg font-medium text-yellow-800">
                                {{ __('Section d\'Administration') }}
                            </h3>
                            <div class="mt-2 text-yellow-700">
                                <p>Vous avez accès aux fonctionnalités d'administration.</p>
                            </div>

                            <!-- Gestion des catégories avec accès direct à la création -->
                            <div class="mt-6 bg-white p-4 rounded-lg shadow-sm border border-yellow-200">
                                <h4 class="font-medium text-yellow-800 mb-3">{{ __('Gestion des catégories') }}</h4>
                                <div class="flex flex-col md:flex-row md:items-center gap-4">
                                    <a href="{{ route('admin.categories.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Voir toutes les catégories') }}
                                    </a>
                                    <a href="{{ route('admin.categories.create') }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __('Ajouter une catégorie') }}
                                    </a>
                                </div>
                            </div>

                            <!-- Autres fonctions d'administration -->
                            <div class="mt-4">
                                <h4 class="font-medium text-yellow-800 mb-3">{{ __('Autres fonctions') }}</h4>
                                <a href="{{ route('admin.users.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Gestion des utilisateurs') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Bienvenue dans votre tableau de bord') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Gérez vos cartes et créez de nouveaux contenus.') }}
                        </p>
                    </div>

                    <div class="mt-8 mb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Mes cartes récentes') }}</h4>

                        {{-- Récupérer les cartes récentes de l'utilisateur --}}
                        @php
                            $recentCards = Auth::user()->cards()->latest()->take(3)->get();
                        @endphp

                        @if ($recentCards->isEmpty())
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-700">Vous n'avez pas encore créé de cartes.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach ($recentCards as $card)
                                    <div
                                        class="bg-gray-50 p-4 rounded-md shadow-sm hover:shadow-md transition duration-200">
                                        <h5 class="font-semibold mb-2">{{ $card->title }}</h5>

                                        {{-- Utiliser les thumbnails optimisés pour les médias --}}
                                        <div class="h-32 w-full mb-2 rounded-md overflow-hidden">
                                            @if ($card->hasMedia('images'))
                                                <img src="{{ $card->getFirstMediaUrl() }}" alt="{{ $card->title }}"
                                                    class="w-full h-full object-cover">
                                            @elseif ($card->hasMedia('videos'))
                                                <div
                                                    class="relative w-full h-full bg-black flex items-center justify-center">
                                                    @if ($card->getFirstMedia('videos')->hasGeneratedConversion('thumb'))
                                                        <img src="{{ $card->getFirstMedia('videos')->getUrl('thumb') }}"
                                                            alt="Aperçu vidéo"
                                                            class="max-w-full max-h-full object-contain">
                                                    @else
                                                        <div
                                                            class="bg-gray-200 w-full h-full flex items-center justify-center">
                                                            <span class="text-gray-500">Aperçu vidéo</span>
                                                        </div>
                                                    @endif
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <div
                                                            class="w-10 h-10 bg-white bg-opacity-75 rounded-full flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 text-indigo-600" fill="none"
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
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <div class="text-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-10 w-10 text-indigo-400 mx-auto" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                        </svg>
                                                        <span class="text-xs text-gray-700">Fichier audio</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-gray-500">Aucun média</span>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($card->description, 100) }}
                                        </p>
                                        <a href="{{ route('cards.show', $card) }}"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Voir la carte →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-indigo-50 p-6 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-indigo-800 mb-2">Créer une nouvelle carte</h4>
                            <p class="text-indigo-600 mb-4">Ajoutez de nouveaux contenus à votre collection avec images,
                                vidéos ou musique.</p>
                            <a href="{{ route('cards.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Créer une carte
                            </a>
                        </div>

                        <div class="bg-emerald-50 p-6 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-emerald-800 mb-2">Gérer mes cartes</h4>
                            <p class="text-emerald-600 mb-4">Consultez, modifiez ou supprimez vos cartes existantes.</p>
                            <a href="{{ route('cards.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Voir toutes mes cartes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
