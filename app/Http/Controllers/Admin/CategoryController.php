<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'enabled' => 'nullable|boolean',
        ]);

        // Si enabled n'est pas présent dans la requête ou s'il a une valeur nulle, on le définit à false
        $validated['enabled'] = $request->boolean('enabled', false);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été créée avec succès.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'enabled' => 'nullable|boolean',
        ]);

        // Si enabled n'est pas présent dans la requête ou s'il a une valeur nulle, on le définit à false
        $validated['enabled'] = $request->boolean('enabled', false);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été mise à jour avec succès.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Vérifier que l'utilisateur connecté est un administrateur
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        // Vérifier si des cartes sont associées à cette catégorie
        if ($category->cards()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des cartes.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été supprimée avec succès.');
    }
}
