{{-- Formulaire pour les cartes --}}
<form method="POST" action="{{ isset($card) ? route('cards.update', $card) : route('cards.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if (isset($card))
        @method('PUT')
    @endif

    {{-- Message d'erreur pour les médias --}}
    @error('media')
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
            <strong>Erreur :</strong> {{ $message }}
        </div>
    @enderror

    <div class="grid grid-cols-1 gap-6">
        {{-- Titre --}}
        <div>
            <x-input-label for="title" :value="__('Titre')" />
            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', isset($card) ? $card->title : '')" required
                autofocus />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        {{-- Description --}}
        <div>
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" rows="4"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>{{ old('description', isset($card) ? $card->description : '') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        {{-- Catégorie --}}
        <div>
            <x-input-label for="category_id" :value="__('Catégorie')" />
            <select id="category_id" name="category_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">Sélectionner une catégorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', isset($card) ? $card->category_id : '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
        </div>

        {{-- Taille de la carte --}}
        <div>
            <x-input-label for="card_size_id" :value="__('Taille de la carte')" />
            @if (auth()->user()->role === 'admin')
                <select id="card_size_id" name="card_size_id"
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                    <option value="">Sélectionner une taille</option>
                    @foreach ($cardSizes as $size)
                        <option value="{{ $size->id }}"
                            {{ old('card_size_id', isset($card) ? $card->card_size_id : '') == $size->id ? 'selected' : '' }}>
                            {{ $size->name }}
                        </option>
                    @endforeach
                </select>
            @else
                <div class="mt-1 w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-100">
                    @if (isset($card) && $card->cardSize)
                        {{ $card->cardSize->name }} <span class="text-gray-500">(La taille ne peut être modifiée que par
                            un administrateur)</span>
                    @else
                        Petit <span class="text-gray-500">(La taille par défaut, ne peut être modifiée que par un
                            administrateur)</span>
                    @endif
                    <input type="hidden" name="card_size_id"
                        value="{{ isset($card) ? $card->card_size_id : $cardSizes->where('name', 'Petit')->first()->id ?? $cardSizes->first()->id }}">
                </div>
            @endif
            <x-input-error :messages="$errors->get('card_size_id')" class="mt-2" />
        </div>

        {{-- Image --}}
        <div>
            <x-input-label for="image" :value="__('Image')" />
            <input id="image" type="file" name="image"
                class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm px-3 py-2" accept="image/*" />
            <x-input-error :messages="$errors->get('image')" class="mt-2" />

            @if (isset($card) && $card->hasMedia('images'))
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Image actuelle:</p>
                    <img src="{{ $card->getFirstMediaUrl('images') }}" alt="Image actuelle"
                        class="h-32 w-auto mt-1 rounded-md">
                    <div class="mt-2">
                        <label class="inline-flex items-center text-sm text-red-600">
                            <input type="checkbox" name="remove_image" value="1" class="media-removal-checkbox"
                                data-media-type="image">
                            <span class="ml-2">Supprimer cette image</span>
                        </label>
                    </div>
                </div>
            @endif
        </div>

        {{-- Vidéo --}}
        <div>
            <x-input-label for="video" :value="__('Vidéo')" />
            <input id="video" type="file" name="video"
                class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm px-3 py-2" accept="video/*" />
            <x-input-error :messages="$errors->get('video')" class="mt-2" />

            @if (isset($card) && $card->hasMedia('videos'))
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Vidéo actuelle:</p>
                    <video controls class="h-32 w-auto mt-1 rounded-md">
                        <source src="{{ $card->getFirstMediaUrl('videos') }}"
                            type="{{ $card->getFirstMedia('videos')->mime_type }}">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                    <div class="mt-2">
                        <label class="inline-flex items-center text-sm text-red-600">
                            <input type="checkbox" name="remove_video" value="1" class="media-removal-checkbox"
                                data-media-type="video">
                            <span class="ml-2">Supprimer cette vidéo</span>
                        </label>
                    </div>
                </div>
            @endif
        </div>

        {{-- Musique --}}
        <div>
            <x-input-label for="music" :value="__('Musique')" />
            <input id="music" type="file" name="music"
                class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm px-3 py-2" accept="audio/*" />
            <x-input-error :messages="$errors->get('music')" class="mt-2" />

            @if (isset($card) && $card->hasMedia('music'))
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Musique actuelle:</p>
                    <audio controls class="w-full mt-1">
                        <source src="{{ $card->getFirstMediaUrl('music') }}"
                            type="{{ $card->getFirstMedia('music')->mime_type }}">
                        Votre navigateur ne supporte pas la lecture audio.
                    </audio>
                    <div class="mt-2">
                        <label class="inline-flex items-center text-sm text-red-600">
                            <input type="checkbox" name="remove_music" value="1" class="media-removal-checkbox"
                                data-media-type="music">
                            <span class="ml-2">Supprimer cette musique</span>
                        </label>
                    </div>
                </div>
            @endif
        </div>

        {{-- Boutons d'action --}}
        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('cards.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                Annuler
            </a>

            <x-primary-button>
                {{ isset($card) ? 'Mettre à jour' : 'Créer' }}
            </x-primary-button>
        </div>
    </div>
</form>

<script>
    // Fonction pour gérer les combinaisons de médias autorisées
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const videoInput = document.getElementById('video');
        const musicInput = document.getElementById('music');

        // Récupérer les cases à cocher de suppression de média
        const removalCheckboxes = document.querySelectorAll('.media-removal-checkbox');

        // Fonction pour mettre à jour l'état des inputs selon les règles
        // Règles autorisées : Image seule, Son seul, Vidéo seule, ou Image + Son
        function updateInputStates() {
            // Vérifier si une vidéo est sélectionnée
            const hasVideo = videoInput.files.length > 0;
            // Vérifier si une vidéo est déjà présente (et non marquée pour suppression)
            const hasExistingVideo = document.querySelector(
                '.media-removal-checkbox[data-media-type="video"]') &&
                !document.querySelector('.media-removal-checkbox[data-media-type="video"]').checked;

            // Si une vidéo est sélectionnée ou présente, désactiver les autres inputs
            if (hasVideo || hasExistingVideo) {
                // Désactiver les inputs image et musique
                imageInput.disabled = true;
                musicInput.disabled = true;

                // Ajouter des messages explicatifs
                addDisabledMessage(imageInput, 'La vidéo ne peut pas être combinée avec une image');
                addDisabledMessage(musicInput, 'La vidéo ne peut pas être combinée avec du son');
            } else {
                // Si pas de vidéo, tout est autorisé
                imageInput.disabled = false;
                musicInput.disabled = false;

                // Supprimer les messages d'erreur
                removeDisabledMessage(imageInput);
                removeDisabledMessage(musicInput);
            }
        }

        // Fonction pour ajouter un message d'avertissement
        function addDisabledMessage(input, message) {
            const parentDiv = input.closest('div');
            if (!parentDiv.querySelector('.media-disabled-message')) {
                const messageEl = document.createElement('p');
                messageEl.className = 'text-sm text-amber-600 mt-1 media-disabled-message';
                messageEl.textContent = message;
                parentDiv.appendChild(messageEl);
            }
        }

        // Fonction pour supprimer un message d'avertissement
        function removeDisabledMessage(input) {
            const message = input.closest('div').querySelector('.media-disabled-message');
            if (message) {
                message.remove();
            }
        }

        // Appliquer la logique quand un fichier est sélectionné
        imageInput.addEventListener('change', updateInputStates);
        videoInput.addEventListener('change', updateInputStates);
        musicInput.addEventListener('change', updateInputStates);

        // Initialiser l'état des inputs au chargement de la page
        updateInputStates();

        // Ajouter des gestionnaires pour les cases à cocher de suppression
        removalCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateInputStates();
            });
        });
    });
</script>
