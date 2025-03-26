<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', "Le rôle de l'utilisateur a été mis à jour avec succès.");
    }
}
