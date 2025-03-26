<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Card hover effects */
        .card-hover:hover .card-image-hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .card-content-hover {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .card-hover:hover .card-content-hover {
            opacity: 1;
            transform: translateY(0);
        }

        .media-icon {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-icon-svg {
            width: 16px;
            height: 16px;
            color: #4f46e5;
        }
    </style>
</head>

<body class="min-h-screen bg-white" x-data="bentoGrid()">
    <!-- Header -->
    <header class="sticky top-0 z-10 border-b bg-white py-4">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-full bg-indigo-600"></div>
                    <h1 class="text-xl font-bold text-indigo-600">{{ config('app.name', 'Laravel') }}</h1>
                </a>
            </div>

            <div class="flex items-center gap-4">
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

                <div class="flex flex-wrap gap-2">
                    <button @click="setAllToSize('small')"
                        class="px-3 py-1.5 border rounded-md text-sm hover:bg-gray-100">
                        Toutes Petites
                    </button>
                    <button @click="setAllToSize('medium')"
                        class="px-3 py-1.5 border rounded-md text-sm hover:bg-gray-100">
                        Toutes Moyennes
                    </button>
                    <button @click="setAllToSize('large')"
                        class="px-3 py-1.5 border rounded-md text-sm hover:bg-gray-100">
                        Toutes Grandes
                    </button>
                    <button @click="resetToDefaults()"
                        class="px-3 py-1.5 bg-indigo-500 text-white border border-transparent rounded-md text-sm hover:bg-indigo-600">
                        Tailles originales
                    </button>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                            <a href="{{ route('cards.index') }}" class="text-gray-700 hover:text-gray-900">Mes Cartes</a>
                            <a href="{{ route('cards.create') }}"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Créer une
                                carte</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Se connecter</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">S'inscrire</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse ($cards as $card)
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
                    $colorIndex = isset($card->category_id)
                        ? $card->category_id % count($categoryColors)
                        : rand(0, count($categoryColors) - 1);
                    $bgColor = $categoryColors[$colorIndex];
                @endphp

                <div x-data="{ cardId: {{ $card->id }} }"
                    :class="{
                        'col-span-1 row-span-1': itemSizes[cardId] === 'small',
                        'col-span-1 md:col-span-2 row-span-1': itemSizes[cardId] === 'medium',
                        'col-span-1 md:col-span-2 row-span-2': itemSizes[cardId] === 'large'
                    }"
                    class="rounded-xl overflow-hidden shadow-sm transition-all duration-200 border {{ $bgColor }}">
                    <div class="h-full flex flex-col">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-full bg-white/90 backdrop-blur-sm">
                                        @if ($card->hasMedia('images'))
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @elseif ($card->hasMedia('videos'))
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        @elseif ($card->hasMedia('music'))
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-medium">{{ $card->title }}</h3>
                                </div>
                                <span class="text-xs px-2 py-1 bg-white/90 rounded-full">
                                    {{ ucfirst($card->cardSize->name) }}
                                </span>
                            </div>

                            <div class="overflow-hidden rounded-md group cursor-pointer"
                                :class="{
                                    'h-40': itemSizes[cardId] === 'small',
                                    'h-52': itemSizes[cardId] === 'medium',
                                    'h-64': itemSizes[cardId] === 'large'
                                }">
                                @if ($card->hasMedia('images'))
                                    <div class="relative h-full w-full">
                                        <img src="{{ $card->getFirstMediaUrl('images', 'grid') }}"
                                            alt="{{ $card->title }}"
                                            class="h-full w-full object-cover card-image-hover transition-transform duration-300">
                                        <div
                                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-white card-content-hover">
                                            <p class="text-sm">{{ Str::limit($card->description, 100) }}</p>
                                            @if ($card->category)
                                                <span
                                                    class="mt-2 inline-block rounded-full bg-white/20 px-2 py-1 text-xs">{{ $card->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($card->hasMedia('videos'))
                                    <div class="relative h-full w-full">
                                        @if ($card->getFirstMedia('videos')->hasGeneratedConversion('thumb'))
                                            <img src="{{ $card->getFirstMedia('videos')->getUrl('thumb') }}"
                                                alt="Aperçu vidéo"
                                                class="h-full w-full object-cover card-image-hover transition-transform duration-300">
                                        @else
                                            <div class="bg-gray-200 w-full h-full flex items-center justify-center">
                                                <span class="text-gray-500">Aperçu en cours de génération</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div
                                                class="w-12 h-12 bg-white bg-opacity-75 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-indigo-600" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div
                                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-white card-content-hover">
                                            <p class="text-sm">{{ Str::limit($card->description, 100) }}</p>
                                            @if ($card->category)
                                                <span
                                                    class="mt-2 inline-block rounded-full bg-white/20 px-2 py-1 text-xs">{{ $card->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($card->hasMedia('music'))
                                    <div class="w-full h-full bg-white/80 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-16 w-16 text-indigo-400 mx-auto mb-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                            <span
                                                class="text-sm text-gray-700">{{ $card->getFirstMedia('music')->file_name }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-full bg-white/80 flex items-center justify-center">
                                        <span class="text-gray-500">Aucun média</span>
                                    </div>
                                @endif
                            </div>

                            <p class="text-gray-600 my-4 line-clamp-2 flex-grow">
                                {{ Str::limit($card->description, 150) }}
                            </p>

                            <div class="flex justify-between items-center mt-auto">
                                @if ($card->category)
                                    <span
                                        class="text-sm bg-white/80 text-gray-800 px-2 py-1 rounded">{{ $card->category->name }}</span>
                                @else
                                    <span class="text-sm bg-white/80 text-gray-800 px-2 py-1 rounded">Non
                                        catégorisé</span>
                                @endif
                                <a href="{{ route('cards.show', $card) }}"
                                    class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Voir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
    </main>

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
</body>

</html>
