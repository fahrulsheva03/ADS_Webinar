<?php

namespace Database\Factories;

use App\Models\Ebook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ebook>
 */
class EbookFactory extends Factory
{
    protected $model = Ebook::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'description' => fake()->paragraph(),
            'cover_image' => null,
            'pdf_file' => null,
            'price' => fake()->randomFloat(2, 0, 500000),
            'stock' => fake()->numberBetween(0, 200),
            'is_active' => true,
        ];
    }
}
