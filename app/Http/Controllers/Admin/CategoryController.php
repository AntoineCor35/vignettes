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
    public function index(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'enabled' => 'nullable|boolean',
        ]);

        $validated['enabled'] = $request->boolean('enabled', false);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été créée avec succès.');
    }

    public function edit(Category $category): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'enabled' => 'nullable|boolean',
        ]);

        $validated['enabled'] = $request->boolean('enabled', false);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été mise à jour avec succès.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        if ($category->cards()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée par des cartes.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'La catégorie a été supprimée avec succès.');
    }
}
