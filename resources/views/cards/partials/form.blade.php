{{-- Formulaire pour les cartes --}}
<form method="POST" action="{{ isset($card) ? route('cards.update', $card) : route('cards.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if (isset($card))
        @method('PUT')
    @endif

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
