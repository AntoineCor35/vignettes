<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CardPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Les administrateurs peuvent tout faire
        if ($user->isAdmin()) {
            return true;
        }

        // Retourner null pour passer aux vérifications spécifiques
        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent voir la liste de leurs cartes
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Card $card): bool
    {
        // Un utilisateur peut voir ses propres cartes
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent créer des cartes
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Card $card): bool
    {
        // Un utilisateur peut mettre à jour ses propres cartes
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can change the card size.
     */
    public function changeCardSize(User $user): bool
    {
        // Seuls les administrateurs peuvent changer la taille des cartes
        // Cette vérification est redondante avec before() mais assure une meilleure clarté
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Card $card): bool
    {
        // Un utilisateur peut supprimer ses propres cartes
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Card $card): bool
    {
        // Un utilisateur peut restaurer ses propres cartes
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Card $card): bool
    {
        // Seuls les administrateurs peuvent supprimer définitivement des cartes
        // (Cette vérification est redondante avec before() mais assure une meilleure clarté)
        return $user->isAdmin();
    }
}
