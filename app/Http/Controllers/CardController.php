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
        try {
            // Vérifie si l'utilisateur est autorisé à créer des cartes
            $this->authorize('create', Card::class);

            // Debug: Afficher des informations sur le fichier vidéo
            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                $videoInfo = [
                    'filename' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime' => $videoFile->getMimeType(),
                    'extension' => $videoFile->getClientOriginalExtension(),
                    'is_valid' => $videoFile->isValid(),
                    'error_code' => $videoFile->getError(),
                    'path' => $videoFile->getPathname(),
                ];
                \Illuminate\Support\Facades\Log::info('Informations sur la vidéo uploadée:', $videoInfo);
            }

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
                'video' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv,flv,m4v,3gp|max:102400',
                'music' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:20480',
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
                try {
                    $card->addMediaFromRequest('image')
                        ->toMediaCollection('images');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur lors de l\'upload d\'image: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage()]);
                }
            }

            if ($hasVideo) {
                try {
                    // Essayer une approche différente pour l'upload de vidéo
                    $videoFile = $request->file('video');
                    $card->addMedia($videoFile->getPathname())
                        ->usingName(pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME))
                        ->usingFileName($videoFile->hashName())
                        ->withCustomProperties(['mime-type' => $videoFile->getMimeType()])
                        ->toMediaCollection('videos');

                    \Illuminate\Support\Facades\Log::info('Vidéo uploadée avec succès');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur détaillée lors de l\'upload de vidéo: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['video' => 'Erreur lors de l\'upload de la vidéo: ' . $e->getMessage()]);
                }
            }

            if ($hasMusic) {
                try {
                    $card->addMediaFromRequest('music')
                        ->toMediaCollection('music');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur lors de l\'upload de musique: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['music' => 'Erreur lors de l\'upload de l\'audio: ' . $e->getMessage()]);
                }
            }

            return redirect()->route('cards.show', $card)
                ->with('success', 'Carte créée avec succès!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur globale lors de la création de carte: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Une erreur est survenue lors de la création de la carte: ' . $e->getMessage()]);
        }
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
        try {
            // Vérifie si l'utilisateur est autorisé à mettre à jour la carte
            $this->authorize('update', $card);

            // Debug: Afficher des informations sur le fichier vidéo
            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                $videoInfo = [
                    'filename' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime' => $videoFile->getMimeType(),
                    'extension' => $videoFile->getClientOriginalExtension(),
                    'is_valid' => $videoFile->isValid(),
                    'error_code' => $videoFile->getError(),
                    'path' => $videoFile->getPathname(),
                ];
                \Illuminate\Support\Facades\Log::info('Informations sur la vidéo uploadée (update):', $videoInfo);
            }

            // Vérifier les combinaisons de médias
            $hasImageRequest = $request->hasFile('image') && $request->file('image')->isValid();
            $hasVideoRequest = $request->hasFile('video') && $request->file('video')->isValid();
            $hasMusicRequest = $request->hasFile('music') && $request->file('music')->isValid();

            // Vérifier si des fichiers ont été téléchargés mais sont invalides
            if ($request->hasFile('image') && !$request->file('image')->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Le fichier image est invalide ou trop volumineux. Taille maximale: 10MB.']);
            }

            if ($request->hasFile('video') && !$request->file('video')->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['video' => 'Le fichier vidéo est invalide ou trop volumineux. Taille maximale: 100MB.']);
            }

            if ($request->hasFile('music') && !$request->file('music')->isValid()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['music' => 'Le fichier audio est invalide ou trop volumineux. Taille maximale: 20MB.']);
            }

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
                'video' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv,flv,m4v,3gp|max:102400',
                'music' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:20480',
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
                try {
                    // Si on ajoute une vidéo, supprimer tous les autres médias
                    $card->clearMediaCollection('images');
                    $card->clearMediaCollection('music');
                    $card->clearMediaCollection('videos');

                    // Essayer une approche différente pour l'upload de vidéo
                    $videoFile = $request->file('video');
                    $card->addMedia($videoFile->getPathname())
                        ->usingName(pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME))
                        ->usingFileName($videoFile->hashName())
                        ->withCustomProperties(['mime-type' => $videoFile->getMimeType()])
                        ->toMediaCollection('videos');

                    \Illuminate\Support\Facades\Log::info('Vidéo mise à jour avec succès');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur détaillée lors de l\'upload de vidéo (update): ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['video' => 'Erreur lors de l\'upload de la vidéo: ' . $e->getMessage()]);
                }
            } else {
                // Sinon, traiter les autres médias
                if ($hasImageRequest) {
                    try {
                        $card->clearMediaCollection('images');
                        $card->addMediaFromRequest('image')
                            ->toMediaCollection('images');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Erreur lors de l\'upload d\'image (update): ' . $e->getMessage());
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['image' => 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage()]);
                    }
                }

                if ($hasMusicRequest) {
                    try {
                        $card->clearMediaCollection('music');
                        $card->addMediaFromRequest('music')
                            ->toMediaCollection('music');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Erreur lors de l\'upload de musique (update): ' . $e->getMessage());
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['music' => 'Erreur lors de l\'upload de l\'audio: ' . $e->getMessage()]);
                    }
                }
            }

            return redirect()->route('cards.show', $card)
                ->with('success', 'Carte mise à jour avec succès!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur globale lors de la mise à jour de carte: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Une erreur est survenue lors de la mise à jour de la carte: ' . $e->getMessage()]);
        }
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
