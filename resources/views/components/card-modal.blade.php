@props(['card', 'modalId', 'bgColor', 'showActions' => false])

<x-modal :id="$modalId" maxWidth="4xl">
    <div class="flex flex-col">
        <!-- Header de la modal -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full {{ $bgColor }}">
                    @if ($card->hasMedia('images'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('videos'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    @elseif ($card->hasMedia('music'))
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                </div>
                <h3 class="text-xl font-medium">{{ $card->title }}</h3>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Media de la carte -->
            <div class="w-full md:w-2/3 overflow-hidden rounded-lg">
                @if ($card->hasMedia('images'))
                    <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
                        class="w-full h-auto object-cover" />
                @elseif ($card->hasMedia('videos'))
                    <div class="relative w-full aspect-video bg-black flex items-center justify-center"
                        x-data="{
                            videoReady: false,
                            setupVideo() {
                                const video = this.$refs.modalVideo;
                                video.addEventListener('canplay', () => {
                                    this.videoReady = true;
                                    if ($el.closest('[x-data]').show) {
                                        video.play();
                                    }
                                });
                            }
                        }" x-init="setupVideo()"
                        @open-modal.window="if ($event.detail == '{{ $modalId }}' && videoReady) { $nextTick(() => $refs.modalVideo.play()); }"
                        @close-modal.window="if ($event.detail == '{{ $modalId }}') { $refs.modalVideo.pause(); }">
                        <video src="{{ $card->getFirstMediaUrl('videos') }}" class="w-full h-auto" controls
                            preload="auto" x-ref="modalVideo"></video>
                    </div>
                @elseif ($card->hasMedia('music'))
                    <div class="w-full p-8 bg-white/80 flex items-center justify-center rounded-lg border">
                        <div class="text-center w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-indigo-400 mx-auto mb-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                            <div class="text-lg text-gray-700 mb-4">{{ $card->getFirstMedia('music')->file_name }}
                            </div>
                            <audio controls class="w-full">
                                <source src="{{ $card->getFirstMediaUrl('music') }}"
                                    type="{{ $card->getFirstMedia('music')->mime_type }}">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                        </div>
                    </div>
                @else
                    <div class="w-full h-64 bg-white/80 flex items-center justify-center rounded-lg border">
                        <span class="text-gray-500 text-lg">Aucun média disponible</span>
                    </div>
                @endif
            </div>

            <!-- Informations de la carte -->
            <div class="w-full md:w-1/3 flex flex-col">
                @if (auth()->check() && auth()->user()->role === 'admin' && auth()->id() !== $card->user_id)
                    <div class="mb-4 bg-yellow-50 text-yellow-700 px-4 py-2 rounded-md">
                        <div class="font-medium">Propriétaire:</div>
                        <div>{{ $card->user->name }}</div>
                        <div class="text-sm">{{ $card->user->email }}</div>
                    </div>
                @endif

                <div class="mb-4">
                    <h3 class="text-lg font-medium border-b pb-2 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $card->description }}</p>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-medium border-b pb-2 mb-2">Détails</h3>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                        <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('home', ['category' => $card->category_id]) }}"
                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                @click="$dispatch('close-modal', '{{ $modalId }}')">
                                {{ $card->category ? $card->category->name : 'Non catégorisé' }}
                            </a>
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Auteur</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('home', ['author' => $card->user_id]) }}"
                                class="text-indigo-600 hover:text-indigo-800 hover:underline"
                                @click="$dispatch('close-modal', '{{ $modalId }}')">
                                {{ $card->user->name }}
                            </a>
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Taille</dt>
                        <dd class="text-sm text-gray-900">{{ $card->cardSize ? $card->cardSize->name : 'Standard' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">Date de création</dt>
                        <dd class="text-sm text-gray-900">{{ $card->creation_date->format('d/m/Y') }}</dd>
                    </dl>
                </div>

                <div class="mt-auto pt-4 border-t">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('cards.show', $card) }}"
                            class="flex-1 flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none">
                            Voir les détails complets
                        </a>

                        @if ($showActions && auth()->check() && (auth()->id() === $card->user_id || auth()->user()->role === 'admin'))
                            <a href="{{ route('cards.edit', $card) }}"
                                class="flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none"
                                @click.stop>
                                Éditer
                            </a>

                            <form action="{{ route('cards.destroy', $card) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte ?')">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-modal>
