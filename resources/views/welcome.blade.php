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
        /* Masonry Grid Styles */
        .masonry-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            grid-gap: 16px;
        }

        @media (min-width: 640px) {
            .masonry-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (min-width: 768px) {
            .masonry-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .masonry-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                grid-auto-flow: dense;
            }

            .masonry-small {
                grid-row-end: span 1;
            }

            .masonry-medium {
                grid-row-end: span 2;
            }

            .masonry-large {
                grid-row-end: span 3;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-10 border-b bg-white py-4">
        <div class="container mx-auto flex items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-red-600"></div>
                <h1 class="text-xl font-bold text-red-600">{{ config('app.name', 'Laravel') }}</h1>
            </div>

            <div class="flex items-center gap-4">
                <button id="shuffle-btn"
                    class="flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 3 21 3 21 8"></polyline>
                        <line x1="4" y1="20" x2="21" y2="3"></line>
                        <polyline points="21 16 21 21 16 21"></polyline>
                        <line x1="15" y1="15" x2="21" y2="21"></line>
                        <line x1="4" y1="4" x2="9" y2="9"></line>
                    </svg>
                    Shuffle
                </button>

                @if (Route::has('login'))
                    <div class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                            <a href="{{ route('cards.create') }}"
                                class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Create Card</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="container mx-auto px-4 py-8">
        <div class="masonry-grid">
            @forelse ($cards as $card)
                <div
                    class="masonry-item mb-4 overflow-hidden rounded-xl 
                    {{ $card->cardSize->name === 'small'
                        ? 'masonry-small'
                        : ($card->cardSize->name === 'medium'
                            ? 'masonry-medium'
                            : 'masonry-large') }}">
                    <div class="group relative h-full w-full cursor-pointer card-hover">
                        <div class="w-full overflow-hidden
                            {{ $card->cardSize->name === 'small'
                                ? 'h-[200px]'
                                : ($card->cardSize->name === 'medium'
                                    ? 'h-[300px]'
                                    : 'h-[400px]') }}"
                            style="background-color: #{{ dechex(crc32($card->title)) }};">

                            @if ($card->getFirstMedia('images'))
                                <img src="{{ $card->getFirstMedia('images')->getUrl() }}" alt="{{ $card->title }}"
                                    class="h-full w-full object-cover card-image-hover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gray-200">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                            @endif

                            @if ($card->getFirstMedia('videos'))
                                <div class="media-icon top-2 right-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="media-icon-svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif

                            @if ($card->getFirstMedia('music'))
                                <div class="media-icon top-2 left-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="media-icon-svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-white card-content-hover">
                            <h3 class="font-bold">{{ $card->title }}</h3>
                            <p class="text-sm">{{ Str::limit($card->description, 100) }}</p>
                            @if ($card->category)
                                <span
                                    class="mt-2 inline-block rounded-full bg-white/20 px-2 py-1 text-xs">{{ $card->category->name }}</span>
                            @endif
                        </div>

                        <a href="{{ route('cards.show', $card) }}" class="absolute inset-0">
                            <span class="sr-only">View {{ $card->title }}</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <h3 class="text-lg font-medium text-gray-900">No cards found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new card.</p>
                    @auth
                        <div class="mt-6">
                            <a href="{{ route('cards.create') }}"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                                Create your first card
                            </a>
                        </div>
                    @else
                        <div class="mt-6">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">
                                Login to create cards
                            </a>
                        </div>
                    @endauth
                </div>
            @endforelse
        </div>
    </main>

    <!-- Scripts for masonry layout and shuffle functionality -->
    <script>
        // Check if the browser supports CSS Grid with grid-template-rows: masonry
        if (CSS.supports('grid-template-rows', 'masonry')) {
            document.querySelector('.masonry-grid').style.gridTemplateRows = 'masonry';
        }

        // Shuffle functionality
        document.getElementById('shuffle-btn').addEventListener('click', function() {
            const grid = document.querySelector('.masonry-grid');
            const cards = Array.from(grid.children);

            // Shuffle array
            let shuffled = cards.map(value => ({
                    value,
                    sort: Math.random()
                }))
                .sort((a, b) => a.sort - b.sort)
                .map(({
                    value
                }) => value);

            // Clear the grid and append shuffled cards
            grid.innerHTML = '';
            shuffled.forEach(card => {
                grid.appendChild(card);
            });
        });
    </script>
</body>

</html>
