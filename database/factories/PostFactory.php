<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => fake()->paragraphs(8, true),
            'meta_description' => fake()->sentence(6, true),
            'meta_keywords' => implode(',', fake()->words(3, false)),
            'active' => true,
        ];
    }
}
