<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\CardSize;
use App\Models\Category;
use App\Models\User;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'image' => fake()->word(),
            'music' => fake()->word(),
            'video' => fake()->word(),
            'description' => fake()->text(),
            'deleted' => fake()->boolean(),
            'creation_date' => fake()->date(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'card_size_id' => CardSize::factory(),
        ];
    }
}
