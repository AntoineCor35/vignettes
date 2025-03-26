<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $cards = Card::with(['media', 'cardSize', 'category'])
            ->where('deleted', false)
            ->get();

        return view('welcome', compact('cards'));
    }
}
