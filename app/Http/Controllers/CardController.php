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
            Log::info('Début de la création de carte', ['user_id' => Auth::id()]);

            $this->authorize('create', Card::class);
            Log::info('Autorisation vérifiée');

            $validated = $request->validated();
            Log::info('Données validées', ['validated' => $validated]);

            $hasImage = $request->hasFile('image');
            $hasVideo = $request->hasFile('video');
            $hasMusic = $request->hasFile('music');
            Log::info('Fichiers vérifiés', ['hasImage' => $hasImage, 'hasVideo' => $hasVideo, 'hasMusic' => $hasMusic]);

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
            Log::info('Carte créée', ['card_id' => $card->id]);

            if ($hasImage) {
                Log::info('Ajout de l\'image');
                $card->addMediaFromRequest('image')->toMediaCollection('images');
                Log::info('Image ajoutée');
            }

            if ($hasVideo) {
                Log::info('Ajout de la vidéo');
                $card->addMediaFromRequest('video')->toMediaCollection('videos');
                Log::info('Vidéo ajoutée');
            }

            if ($hasMusic) {
                Log::info('Ajout de la musique');
                $card->addMediaFromRequest('music')->toMediaCollection('music');
                Log::info('Musique ajoutée');
            }

            Log::info('Carte créée avec succès', ['card_id' => $card->id]);
            return redirect()->route('cards.show', $card)->with('success', 'Carte créée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la carte', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
