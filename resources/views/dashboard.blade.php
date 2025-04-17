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
                        <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Mes cartes') }}</h4>

                        {{-- Récupérer les cartes de l'utilisateur --}}
                        @php
                            $userCards =
                                Auth::user()->role === 'admin'
                                    ? App\Models\Card::with('media')->where('deleted', false)->get()
                                    : Auth::user()->cards()->with('media')->where('deleted', false)->get();
                        @endphp

                        @if ($userCards->isEmpty())
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-700">Vous n'avez pas encore créé de cartes.</p>
                            </div>
                        @else
                            <div x-data="bentoGrid()" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @foreach ($userCards as $card)
                                        <x-card :card="$card" :showActions="true" />
                                    @endforeach
                                </div>
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

    <!-- Alpine.js Script -->
    <script>
        function bentoGrid() {
            return {
                itemSizes: {},
                init() {
                    @foreach ($userCards as $card)
                        @if ($card->cardSize->name === 'Petit')
                            this.itemSizes[{{ $card->id }}] = 'small';
                        @elseif ($card->cardSize->name === 'Moyen')
                            this.itemSizes[{{ $card->id }}] = 'wide';
                        @else
                            this.itemSizes[{{ $card->id }}] = 'large';
                        @endif
                    @endforeach
                },
            }
        }
    </script>
</x-app-layout>
