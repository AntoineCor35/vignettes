<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour tous les utilisateurs qui n'ont pas de display_name
        $users = User::whereNull('display_name')->get();

        foreach ($users as $user) {
            do {
                $displayName = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            } while (User::where('display_name', $displayName)->exists());

            $user->update(['display_name' => $displayName]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne fait rien dans le down() car on ne veut pas perdre les display_names
        // si on fait un rollback
    }
};
