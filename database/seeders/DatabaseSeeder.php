<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Card;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {


        $this->call([
            UserSeeder::class,
            CardSizeSeeder::class,
            CategorySeeder::class,
        ]);

        User::factory(5)->create();
    }
}
