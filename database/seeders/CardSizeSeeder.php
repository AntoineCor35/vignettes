<?php

namespace Database\Seeders;

use App\Models\CardSize;
use Illuminate\Database\Seeder;

class CardSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les 3 tailles de cartes par défaut
        $cardSizes = [
            'Petit',
            'Moyen',
            'Grand'
        ];

        foreach ($cardSizes as $name) {
            CardSize::updateOrCreate(
                ['name' => $name],
            );
        }
    }
}
