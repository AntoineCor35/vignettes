<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accueil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="bentoGrid()" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse ($cards as $card)
                            <x-card :card="$card" />
                        @empty
                            <div class="col-span-full py-12 text-center">
                                <h3 class="text-lg font-medium text-gray-900">Aucune carte trouvée</h3>
                                <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle carte.</p>
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
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts pour le bento grid et le shuffle -->
    <script>
        function bentoGrid() {
            return {
                itemSizes: {},
                originalSizes: {},

                init() {
                    // Initialiser les tailles par défaut et conserver les tailles originales
                    @foreach ($cards as $card)
                        // Convertir cardSize.name en taille correspondante pour itemSizes
                        @if ($card->cardSize->name === 'small')
                            this.itemSizes[{{ $card->id }}] = 'small';
                            this.originalSizes[{{ $card->id }}] = 'small';
                        @elseif ($card->cardSize->name === 'medium')
                            this.itemSizes[{{ $card->id }}] = 'medium';
                            this.originalSizes[{{ $card->id }}] = 'medium';
                        @else
                            this.itemSizes[{{ $card->id }}] = 'large';
                            this.originalSizes[{{ $card->id }}] = 'large';
                        @endif
                    @endforeach

                    // Attacher l'événement shuffle au bouton
                    document.getElementById('shuffle-btn').addEventListener('click', this.shuffleCards.bind(this));
                },

                setAllToSize(size) {
                    @foreach ($cards as $card)
                        this.itemSizes[{{ $card->id }}] = size;
                    @endforeach
                },

                resetToDefaults() {
                    // Restaurer toutes les cartes à leur taille d'origine du modèle
                    @foreach ($cards as $card)
                        this.itemSizes[{{ $card->id }}] = this.originalSizes[{{ $card->id }}];
                    @endforeach
                },

                shuffleCards() {
                    const grid = document.querySelector('.grid');
                    const cards = Array.from(grid.children);

                    // Exclure la div affichée quand il n'y a pas de cartes
                    const cardsToShuffle = cards.filter(card => !card.classList.contains('col-span-full'));

                    if (cardsToShuffle.length <= 1) return;

                    // Shuffle array
                    let shuffled = cardsToShuffle.map(value => ({
                            value,
                            sort: Math.random()
                        }))
                        .sort((a, b) => a.sort - b.sort)
                        .map(({
                            value
                        }) => value);

                    // Clear the grid and append shuffled cards
                    shuffled.forEach(card => {
                        grid.appendChild(card);
                    });

                    // Si la div "aucune carte" existe, assurez-vous qu'elle reste à la fin
                    const emptyMessage = cards.find(card => card.classList.contains('col-span-full'));
                    if (emptyMessage) {
                        grid.appendChild(emptyMessage);
                    }
                }
            }
        }
    </script>
</x-app-layout>
