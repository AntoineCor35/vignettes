<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\CardSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CardController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Vérifie si l'utilisateur est autorisé à voir les cartes
        $this->authorize('viewAny', Card::class);

        $user = Auth::user();

        // Les administrateurs peuvent voir toutes les cartes
        if ($user->role === 'admin') {
            $cards = Card::with('media')
                ->where('deleted', false)
                ->get();
        } else {
            // Les utilisateurs normaux ne voient que leurs propres cartes
            $cards = Card::with('media')
                ->where('user_id', Auth::id())
                ->where('deleted', false)
                ->get();
        }

        return view('cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $cardSizes = CardSize::all();

        return view('cards.create', compact('categories', 'cardSizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Vérifie si l'utilisateur est autorisé à créer des cartes
        $this->authorize('create', Card::class);

        // Vérifier les combinaisons de médias autorisées
        $hasImage = $request->hasFile('image');
        $hasVideo = $request->hasFile('video');
        $hasMusic = $request->hasFile('music');

        // Combinaisons invalides : (vidéo + image) ou (vidéo + son)
        if (($hasVideo && $hasImage) || ($hasVideo && $hasMusic)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['media' => 'La vidéo ne peut pas être combinée avec d\'autres médias. Utilisez une vidéo seule ou une combinaison image+son.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'card_size_id' => 'required|exists:card_sizes,id',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:102400',
            'music' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
        ]);

        $card = new Card();
        $card->title = $validated['title'];
        $card->description = $validated['description'];
        $card->category_id = $validated['category_id'];

        // Vérifie si l'utilisateur est autorisé à changer la taille des cartes
        if (Gate::allows('change-card-size')) {
            $card->card_size_id = $validated['card_size_id'];
        } else {
            // Sinon, utilise la taille petite par défaut
            $smallSize = CardSize::where('name', 'Petit')->first();
            $card->card_size_id = $smallSize ? $smallSize->id : $validated['card_size_id'];
        }

        $card->user_id = Auth::id();
        $card->creation_date = now();
        $card->deleted = false;
        $card->save();

        // Gérer les médias avec Spatie
        if ($hasImage) {
            $card->addMediaFromRequest('image')
                ->toMediaCollection('images');
        }

        if ($hasVideo) {
            $card->addMediaFromRequest('video')
                ->toMediaCollection('videos');
        }

        if ($hasMusic) {
            $card->addMediaFromRequest('music')
                ->toMediaCollection('music');
        }

        return redirect()->route('cards.show', $card)
            ->with('success', 'Carte créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        // Vérifie si l'utilisateur est autorisé à voir la carte
        $this->authorize('view', $card);

        return view('cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {
        // Vérifie si l'utilisateur est autorisé à éditer la carte
        $this->authorize('update', $card);

        $categories = Category::all();
        $cardSizes = CardSize::all();

        return view('cards.edit', compact('card', 'categories', 'cardSizes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        // Vérifie si l'utilisateur est autorisé à mettre à jour la carte
        $this->authorize('update', $card);

        // Vérifier les combinaisons de médias
        $hasImageRequest = $request->hasFile('image');
        $hasVideoRequest = $request->hasFile('video');
        $hasMusicRequest = $request->hasFile('music');

        // Suppression des médias demandée
        $removeImage = $request->has('remove_image') && $request->remove_image == 1;
        $removeVideo = $request->has('remove_video') && $request->remove_video == 1;
        $removeMusic = $request->has('remove_music') && $request->remove_music == 1;

        // État actuel des médias (en tenant compte des demandes de suppression)
        $hasImageCurrent = $card->hasMedia('images') && !$removeImage;
        $hasVideoCurrent = $card->hasMedia('videos') && !$removeVideo;
        $hasMusicCurrent = $card->hasMedia('music') && !$removeMusic;

        // Futur état des médias
        $willHaveImage = $hasImageCurrent || $hasImageRequest;
        $willHaveVideo = $hasVideoCurrent || $hasVideoRequest;
        $willHaveMusic = $hasMusicCurrent || $hasMusicRequest;

        // Vérification des combinaisons invalides : (vidéo + image) ou (vidéo + son)
        if (($willHaveVideo && $willHaveImage) || ($willHaveVideo && $willHaveMusic)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['media' => 'La vidéo ne peut pas être combinée avec d\'autres médias. Utilisez une vidéo seule ou une combinaison image+son.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'card_size_id' => 'required|exists:card_sizes,id',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:102400',
            'music' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
        ]);

        $card->title = $validated['title'];
        $card->description = $validated['description'];
        $card->category_id = $validated['category_id'];

        // Vérifie si l'utilisateur est autorisé à changer la taille des cartes
        if (Gate::allows('change-card-size')) {
            $card->card_size_id = $validated['card_size_id'];
        }

        $card->save();

        // Gérer la suppression des médias si demandé
        if ($removeImage) {
            $card->clearMediaCollection('images');
        }

        if ($removeVideo) {
            $card->clearMediaCollection('videos');
        }

        if ($removeMusic) {
            $card->clearMediaCollection('music');
        }

        // Gérer les médias avec Spatie
        if ($hasVideoRequest) {
            // Si on ajoute une vidéo, supprimer tous les autres médias
            $card->clearMediaCollection('images');
            $card->clearMediaCollection('music');
            $card->clearMediaCollection('videos');

            $card->addMediaFromRequest('video')
                ->toMediaCollection('videos');
        } else {
            // Sinon, traiter les autres médias
            if ($hasImageRequest) {
                $card->clearMediaCollection('images');
                $card->addMediaFromRequest('image')
                    ->toMediaCollection('images');
            }

            if ($hasMusicRequest) {
                $card->clearMediaCollection('music');
                $card->addMediaFromRequest('music')
                    ->toMediaCollection('music');
            }
        }

        return redirect()->route('cards.show', $card)
            ->with('success', 'Carte mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        // Vérifie si l'utilisateur est autorisé à supprimer la carte
        $this->authorize('delete', $card);

        // Suppression logique (soft delete)
        $card->deleted = true;
        $card->save();

        return redirect()->route('cards.index')
            ->with('success', 'Carte supprimée avec succès!');
    }
}
