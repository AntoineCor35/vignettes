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
                                        @if ($card->hasMedia('images'))
                                            <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
                                                class="w-full h-32 object-cover rounded-md mb-2">
                                        @endif
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
