<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $card->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between mb-6">
                        <h3 class="text-2xl font-bold">{{ $card->title }}</h3>
                        @auth
                            <div class="space-x-2">
                                <a href="{{ route('cards.edit', $card) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('cards.destroy', $card) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>

                    {{-- Inclure les médias --}}
                    @include('cards.partials.media')

                    <div class="mb-4">
                        <h4 class="text-lg font-semibold mb-2">Description</h4>
                        <p class="text-gray-700">{{ $card->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Catégorie</h4>
                            <p class="text-gray-700">{{ $card->category->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Taille</h4>
                            <p class="text-gray-700">{{ $card->cardSize->name }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Créé par</h4>
                            <p class="text-gray-700">{{ $card->user->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Date de création</h4>
                            <p class="text-gray-700">{{ $card->creation_date->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        @auth
                            <a href="{{ route('cards.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                Retour à la liste
                            </a>
                        @else
                            <a href="{{ url('/') }}" class="text-indigo-600 hover:text-indigo-900">
                                Retour à l'accueil
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
