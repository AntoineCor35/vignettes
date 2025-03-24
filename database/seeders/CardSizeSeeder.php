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
        CardSize::factory()->count(5)->create();
    }
}
