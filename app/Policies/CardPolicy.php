<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CardPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    public function changeCardSize(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    public function restore(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    public function forceDelete(User $user, Card $card): bool
    {
        return $user->isAdmin();
    }
}
