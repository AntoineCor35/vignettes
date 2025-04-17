<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .grid-item {
            break-inside: avoid;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .pinterest-grid {
            animation: scrollGrid 120s linear infinite;
        }

        @keyframes scrollGrid {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-100%);
            }
        }

        .overlay-gradient {
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.8) 100%);
        }
    </style>
</head>

<body class="font-sans antialiased">
    <main class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
        <!-- Background Pinterest Grid -->
        <div class="absolute inset-0 w-full h-full opacity-40 overflow-hidden">
            <div class="pinterest-grid columns-2 sm:columns-3 md:columns-4 lg:columns-5 gap-4 p-4"
                style="height: 200%;">
                @foreach ($cards as $card)
                    <div class="grid-item shadow-md">
                        @if ($card->hasMedia('images'))
                            <div class="relative overflow-hidden rounded-lg">
                                <img src="{{ $card->getThumbnailUrl() }}" alt="{{ $card->title }}"
                                    class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-3">
                                    <h3 class="text-white text-sm font-medium truncate">{{ $card->title }}</h3>
                                    @if ($card->category)
                                        <span class="text-white/80 text-xs">{{ $card->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @elseif($card->hasMedia('videos'))
                            <div
                                class="relative rounded-lg bg-gradient-to-br from-indigo-600 to-purple-700 p-3 text-white">
                                <div class="flex items-center justify-center h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 text-white mb-2 mx-auto opacity-90" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-center">
                                        <h3 class="text-white text-sm font-medium truncate">{{ $card->title }}</h3>
                                        @if ($card->category)
                                            <span class="text-white/80 text-xs">{{ $card->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif($card->hasMedia('music'))
                            <div class="relative rounded-lg bg-gradient-to-br from-pink-600 to-red-700 p-3 text-white">
                                <div class="flex flex-col items-center justify-center h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mb-2 opacity-90"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <h3 class="text-white text-sm font-medium truncate">{{ $card->title }}</h3>
                                    @if ($card->category)
                                        <span class="text-white/80 text-xs">{{ $card->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="relative rounded-lg bg-gradient-to-br from-gray-500 to-gray-700 p-3 text-white">
                                <div class="flex flex-col items-center justify-center h-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mb-2 opacity-90"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="text-white text-sm font-medium truncate">{{ $card->title }}</h3>
                                    @if ($card->category)
                                        <span class="text-white/80 text-xs">{{ $card->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Si pas assez de cartes en BDD, on complète avec des cartes générées -->
                @if ($cards->count() < 30)
                    <div x-data="pinterestGrid()">
                        <template x-for="item in items" :key="item.id">
                            <div class="grid-item shadow-md" :style="`height: ${item.height}px;`">
                                <div class="w-full h-full bg-gradient-to-br" :class="item.gradientClass">
                                    <div class="w-full h-full flex items-center justify-center p-4">
                                        <div class="text-white text-opacity-90 text-center">
                                            <div class="text-3xl mb-2" x-text="item.icon"></div>
                                            <div class="text-sm font-medium" x-text="item.title"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            </div>
        </div>

        <!-- Overlay gradient -->
        <div class="absolute inset-0 overlay-gradient"></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center justify-center gap-8 px-4 py-16 text-center">
            <div class="w-full max-w-lg">
                <x-application-logo class="w-200 h-auto mx-auto" />
            </div>

            <h1 class="text-4xl md:text-6xl font-bold text-indigo-800">Bienvenue sur
                {{ config('app.name', 'Pinterest') }}</h1>

            <p class="text-xl text-indigo-600 max-w-2xl">
                La plateforme qui vous permet de partager et découvrir des moments instantanément
            </p>

            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                <a href="{{ route('home') }}"
                    class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Découvrir
                </a>
                <a href="{{ route('register') }}"
                    class="px-8 py-3 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors">
                    S'inscrire
                </a>
            </div>
        </div>
    </main>

    <script>
        function pinterestGrid() {
            return {
                items: [],
                gradientClasses: [
                    'from-pink-500 to-rose-500',
                    'from-blue-500 to-indigo-600',
                    'from-green-400 to-teal-500',
                    'from-purple-500 to-violet-600',
                    'from-yellow-400 to-amber-500',
                    'from-red-500 to-rose-600',
                    'from-indigo-500 to-purple-600',
                    'from-teal-400 to-cyan-500',
                    'from-orange-400 to-red-500'
                ],
                icons: ['✨', '🎨', '📷', '🌈', '🌟', '💡', '🎭', '🎬', '📱', '💻', '🎧', '🎮', '📚', '🍕', '🍦', '🏖️',
                    '🌄', '🌿'
                ],
                titles: [
                    'Inspiration', 'Créativité', 'Photographie', 'Design', 'Mode', 'Cuisine',
                    'Voyages', 'Sport', 'Musique', 'Cinéma', 'Technologie', 'Littérature',
                    'Jeux vidéo', 'Art', 'Nature', 'Architecture', 'Style', 'DIY'
                ],
                init() {
                    this.items = this.generateItems();
                },
                generateItems() {
                    const newItems = [];
                    for (let i = 0; i < 30; i++) {
                        const gradientIndex = Math.floor(Math.random() * this.gradientClasses.length);
                        const iconIndex = Math.floor(Math.random() * this.icons.length);
                        const titleIndex = Math.floor(Math.random() * this.titles.length);

                        newItems.push({
                            id: i,
                            height: Math.floor(Math.random() * 150) + 150, // Entre 150px et 300px
                            gradientClass: this.gradientClasses[gradientIndex],
                            icon: this.icons[iconIndex],
                            title: this.titles[titleIndex]
                        });
                    }
                    return newItems;
                }
            };
        }
    </script>
</body>

</html>
