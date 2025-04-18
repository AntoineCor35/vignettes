<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\CardSize;
use App\Http\Requests\CardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        $this->authorize('create', Card::class);

        $validated = $request->validated();
        $hasImage = $request->hasFile('image');
        $hasVideo = $request->hasFile('video');
        $hasMusic = $request->hasFile('music');

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

        if ($hasImage) {
            $card->addMediaFromRequest('image')->toMediaCollection('images');
        }

        if ($hasVideo) {
            $card->addMediaFromRequest('video')->toMediaCollection('videos');
        }

        if ($hasMusic) {
            $card->addMediaFromRequest('music')->toMediaCollection('music');
        }

        return redirect()->route('cards.show', $card)->with('success', 'Carte créée avec succès !');
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
