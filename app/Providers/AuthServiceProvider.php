<?php

namespace App\Providers;

use App\Models\Card;
use App\Policies\CardPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Card::class => CardPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Définir un gate spécifique pour la modification de la taille des cartes
        Gate::define('change-card-size', function ($user) {
            return $user->isAdmin();
        });
    }
}
