<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->paragraphs(3, true),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-2 year'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}
