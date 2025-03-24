<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\CardSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Card::with('media')
            ->where('user_id', Auth::id())
            ->where('deleted', false)
            ->get();
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
        $card->card_size_id = $validated['card_size_id'];
        $card->user_id = Auth::id();
        $card->creation_date = now();
        $card->deleted = false;
        $card->save();

        // Gérer les médias avec Spatie
        if ($request->hasFile('image')) {
            $card->addMediaFromRequest('image')
                ->toMediaCollection('images');
        }

        if ($request->hasFile('video')) {
            $card->addMediaFromRequest('video')
                ->toMediaCollection('videos');
        }

        if ($request->hasFile('music')) {
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
        // Vérifier que la carte appartient à l'utilisateur connecté
        if ($card->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        return view('cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {
        // Vérifier que la carte appartient à l'utilisateur connecté
        if ($card->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $categories = Category::all();
        $cardSizes = CardSize::all();

        return view('cards.edit', compact('card', 'categories', 'cardSizes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        // Vérifier que la carte appartient à l'utilisateur connecté
        if ($card->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
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
        $card->card_size_id = $validated['card_size_id'];
        $card->save();

        // Gérer les médias avec Spatie
        if ($request->hasFile('image')) {
            $card->clearMediaCollection('images');
            $card->addMediaFromRequest('image')
                ->toMediaCollection('images');
        }

        if ($request->hasFile('video')) {
            $card->clearMediaCollection('videos');
            $card->addMediaFromRequest('video')
                ->toMediaCollection('videos');
        }

        if ($request->hasFile('music')) {
            $card->clearMediaCollection('music');
            $card->addMediaFromRequest('music')
                ->toMediaCollection('music');
        }

        return redirect()->route('cards.show', $card)
            ->with('success', 'Carte mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        // Suppression logique (soft delete)
        $card->deleted = true;
        $card->save();

        return redirect()->route('cards.index')
            ->with('success', 'Carte supprimée avec succès!');
    }
}
