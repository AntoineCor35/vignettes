<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\CardSize;
use App\Http\Requests\CardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Card::class);

        $user = Auth::user();

        if ($user->role === 'admin') {
            $cards = Card::with('media')
                ->where('deleted', false)
                ->get();
        } else {
            $cards = Card::with('media')
                ->where('user_id', Auth::id())
                ->where('deleted', false)
                ->get();
        }

        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        $categories = Category::all();
        $cardSizes = CardSize::all();

        return view('cards.create', compact('categories', 'cardSizes'));
    }

    public function store(CardRequest $request)
    {
        try {
            Log::info('Début de la création de carte', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
                'user_name' => Auth::user()->display_name,
                'request_data' => $request->all()
            ]);

            $this->authorize('create', Card::class);
            Log::info('Autorisation vérifiée');

            $validated = $request->validated();
            Log::info('Données validées', [
                'validated' => $validated,
                'card_size' => CardSize::find($validated['card_size_id']),
                'category' => Category::find($validated['category_id'])
            ]);

            $hasImage = $request->hasFile('image');
            $hasVideo = $request->hasFile('video');
            $hasMusic = $request->hasFile('music');
            Log::info('Fichiers vérifiés', [
                'hasImage' => $hasImage,
                'hasVideo' => $hasVideo,
                'hasMusic' => $hasMusic,
                'files' => $request->allFiles()
            ]);

            // Vérification des permissions de dossier
            Log::info('Vérification des permissions', [
                'storage_path' => storage_path(),
                'public_path' => public_path(),
                'storage_writable' => is_writable(storage_path()),
                'public_writable' => is_writable(public_path())
            ]);

            $card = Card::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'card_size_id' => Gate::allows('change-card-size')
                    ? $validated['card_size_id']
                    : CardSize::where('name', 'Petit')->first()->getKey(),
                'user_id' => Auth::id(),
                'creation_date' => now(),
                'deleted' => false,
            ]);
            Log::info('Carte créée', ['card' => $card->toArray()]);

            if ($hasImage) {
                Log::info('Tentative d\'ajout de l\'image', [
                    'image_details' => [
                        'name' => $request->file('image')->getClientOriginalName(),
                        'size' => $request->file('image')->getSize(),
                        'mime' => $request->file('image')->getMimeType()
                    ]
                ]);
                $card->addMediaFromRequest('image')->toMediaCollection('images');
                Log::info('Image ajoutée avec succès');
            }

            if ($hasVideo) {
                Log::info('Tentative d\'ajout de la vidéo', [
                    'video_details' => [
                        'name' => $request->file('video')->getClientOriginalName(),
                        'size' => $request->file('video')->getSize(),
                        'mime' => $request->file('video')->getMimeType()
                    ]
                ]);
                $card->addMediaFromRequest('video')->toMediaCollection('videos');
                Log::info('Vidéo ajoutée avec succès');
            }

            if ($hasMusic) {
                Log::info('Tentative d\'ajout de la musique', [
                    'music_details' => [
                        'name' => $request->file('music')->getClientOriginalName(),
                        'size' => $request->file('music')->getSize(),
                        'mime' => $request->file('music')->getMimeType()
                    ]
                ]);
                $card->addMediaFromRequest('music')->toMediaCollection('music');
                Log::info('Musique ajoutée avec succès');
            }

            Log::info('Carte créée avec succès', ['card_id' => $card->id]);
            return redirect()->route('cards.show', $card)->with('success', 'Carte créée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la carte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->display_name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role
                ],
                'request' => $request->all()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la création de la carte. Veuillez réessayer.');
        }
    }

    public function show(Card $card)
    {
        return view('cards.show', compact('card'));
    }

    public function edit(Card $card)
    {
        $this->authorize('update', $card);

        $categories = Category::all();
        $cardSizes = CardSize::all();

        return view('cards.edit', compact('card', 'categories', 'cardSizes'));
    }

    public function update(CardRequest $request, Card $card)
    {
        $this->authorize('update', $card);

        $validated = $request->validated();
        $hasImage = $request->hasFile('image');
        $hasVideo = $request->hasFile('video');
        $hasMusic = $request->hasFile('music');

        $removeImage = $request->boolean('remove_image');
        $removeVideo = $request->boolean('remove_video');
        $removeMusic = $request->boolean('remove_music');

        $card->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'card_size_id' => Gate::allows('change-card-size')
                ? $validated['card_size_id']
                : $card->card_size_id,
        ]);

        if ($removeImage) {
            $card->clearMediaCollection('images');
        }

        if ($removeVideo) {
            $card->clearMediaCollection('videos');
        }

        if ($removeMusic) {
            $card->clearMediaCollection('music');
        }

        if ($hasVideo) {
            $card->clearMediaCollection('images');
            $card->clearMediaCollection('music');
            $card->clearMediaCollection('videos');
            $card->addMediaFromRequest('video')->toMediaCollection('videos');
        } else {
            if ($hasImage) {
                $card->clearMediaCollection('images');
                $card->addMediaFromRequest('image')->toMediaCollection('images');
            }

            if ($hasMusic) {
                $card->clearMediaCollection('music');
                $card->addMediaFromRequest('music')->toMediaCollection('music');
            }
        }

        return redirect()->route('cards.show', $card)->with('success', 'Carte mise à jour avec succès !');
    }

    public function destroy(Card $card)
    {
        $this->authorize('delete', $card);

        $card->deleted = true;
        $card->save();

        return redirect()->route('cards.index')
            ->with('success', 'Carte supprimée avec succès!');
    }
}
