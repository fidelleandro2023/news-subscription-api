<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->text(200),
            'content' => $this->faker->paragraphs(5, true),
            'image_url' => $this->faker->optional()->imageUrl(640, 480, 'news'),
            'news_category_id' => NewsCategory::factory(),
            'author_id' => User::factory(),
            'is_published' => $this->faker->boolean(80),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}