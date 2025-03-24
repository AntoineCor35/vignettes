<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['cinema', 'meme', 'creation', 'art', 'style'];
        foreach ($categories as $category) {
            Category::factory()->create(['name' => $category]);
        }
    }
}
