<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil avec des options de filtrage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Initialiser les variables de filtre
        $activeCategory = null;
        $activeAuthor = null;

        $query = Card::with(['media', 'cardSize', 'category', 'user'])
            ->where('deleted', false);

        // Filtrer par catégorie
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
            $activeCategory = Category::find($request->category);
        }

        // Filtrer par auteur
        if ($request->has('author') && $request->author) {
            $query->where('user_id', $request->author);
            $activeAuthor = User::find($request->author);
        }

        $cards = $query->get();

        // Liste des catégories pour le filtre
        $categories = Category::orderBy('name')->get();

        return view('welcome', compact(
            'cards',
            'categories',
            'activeCategory',
            'activeAuthor'
        ));
    }
}
