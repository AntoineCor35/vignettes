{{-- Affichage de l'image --}}
@if ($card->hasMedia('images'))
    <div class="card-image mb-4">
        <img src="{{ $card->getFirstMediaUrl('images') }}" alt="{{ $card->title }}"
            class="rounded-lg shadow-md max-w-full h-auto" />
    </div>
@endif

{{-- Affichage de la vidéo --}}
@if ($card->hasMedia('videos'))
    <div class="card-video mb-4">
        <video controls class="rounded-lg shadow-md max-w-full">
            <source src="{{ $card->getFirstMediaUrl('videos') }}" type="{{ $card->getFirstMedia('videos')->mime_type }}">
            Votre navigateur ne supporte pas la lecture de vidéos.
        </video>
    </div>
@endif

{{-- Affichage de l'audio --}}
@if ($card->hasMedia('music'))
    <div class="card-audio mb-4">
        <audio controls class="w-full">
            <source src="{{ $card->getFirstMediaUrl('music') }}" type="{{ $card->getFirstMedia('music')->mime_type }}">
            Votre navigateur ne supporte pas la lecture audio.
        </audio>
    </div>
@endif
