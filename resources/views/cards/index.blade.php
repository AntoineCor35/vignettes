<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Cartes') }}
            </h2>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('cards.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Créer une Carte') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($cards->isEmpty())
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-500">Aucune carte trouvée</h3>
                            <p class="mt-2 text-sm text-gray-500">Commencez par créer une nouvelle carte.</p>
                            <div class="mt-6">
                                <a href="{{ route('cards.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('Créer une Carte') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div x-data="bentoGrid()" class="space-y-6">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                                <h2 class="text-xl font-semibold">Mes Cartes</h2>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach ($cards as $card)
                                @if (!$card->deleted)
                                    <x-card :card="$card" />
                                @endif
                            @endforeach
                        </div>
                    @endif
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
                    @foreach ($cards as $card)
                        @if (!$card->deleted)
                            @if ($card->cardSize->name === 'Petit')
                                this.itemSizes[{{ $card->id }}] = 'small';
                            @elseif ($card->cardSize->name === 'Moyen')
                                this.itemSizes[{{ $card->id }}] = 'wide';
                            @else
                                this.itemSizes[{{ $card->id }}] = 'large';
                            @endif
                        @endif
                    @endforeach
                },
            }
        }
    </script>
</x-app-layout>
