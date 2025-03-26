{{-- Affichage de l'image --}}
@if ($card->hasMedia('images'))
    <div class="card-image mb-6">
        {{-- Image principale --}}
        <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
            class="rounded-lg shadow-md max-w-full h-auto mb-2" />

        {{-- Informations sur l'image --}}
        <div class="flex items-center justify-between text-sm text-gray-600">
            <span>{{ $card->getFirstMedia('images')->file_name }}</span>
            <span>{{ number_format($card->getFirstMedia('images')->size / 1024, 2) }} KB</span>
        </div>
    </div>
@endif

{{-- Affichage de la vidéo --}}
@if ($card->hasMedia('videos'))
    <div class="card-video mb-6">
        {{-- Lecteur vidéo --}}
        <video controls class="rounded-lg shadow-md max-w-full mb-2">
            <source src="{{ $card->getFirstMediaUrl('videos') }}" type="{{ $card->getFirstMedia('videos')->mime_type }}">
            Votre navigateur ne supporte pas la lecture de vidéos.
        </video>

        {{-- Thumbnails et informations --}}
        <div class="flex items-start justify-between mt-2">
            <div class="flex items-center gap-2">
                @if ($card->getFirstMedia('videos')->hasGeneratedConversion('thumb'))
                    <img src="{{ $card->getFirstMedia('videos')->getUrl('thumb') }}" alt="Aperçu vidéo"
                        class="w-16 h-16 object-cover rounded" />
                @endif
                <span class="text-sm text-gray-600">{{ $card->getFirstMedia('videos')->file_name }}</span>
            </div>
            <span
                class="text-sm text-gray-600">{{ number_format($card->getFirstMedia('videos')->size / (1024 * 1024), 2) }}
                MB</span>
        </div>
    </div>
@endif

{{-- Affichage de l'audio --}}
@if ($card->hasMedia('music'))
    <div class="card-audio mb-6">
        {{-- Lecteur audio --}}
        <div class="bg-gray-100 p-4 rounded-lg shadow-md mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-16 h-16 flex-shrink-0 rounded-md bg-indigo-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                </div>
                <div class="flex-grow">
                    <p class="text-sm font-medium text-gray-900">{{ $card->getFirstMedia('music')->file_name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ number_format($card->getFirstMedia('music')->size / (1024 * 1024), 2) }} MB</p>
                </div>
            </div>
            <audio controls class="w-full">
                <source src="{{ $card->getFirstMediaUrl('music') }}"
                    type="{{ $card->getFirstMedia('music')->mime_type }}">
                Votre navigateur ne supporte pas la lecture audio.
            </audio>
        </div>
    </div>
@endif
